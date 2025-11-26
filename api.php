<?php
// api.php - The Policy Enforcement Point (PEP)
require_once 'config.php';

header('Content-Type: application/json');

// Determine the action (login, view, edit, admin)
$action = $_GET['action'] ?? '';

// --- 1. LOGIN (Public Endpoint) ---
if ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($mock_users[$username]) && $mock_users[$username]['password'] === $password) {
        $user = $mock_users[$username];
        $payload = [
            'sub' => $username,
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + TOKEN_EXPIRY,
        ];
        echo json_encode([
            'status' => 'success', 
            'token' => createMockToken($payload), 
            'role' => $user['role']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
    }
    exit;
}

// --- 2. PROTECTED RESOURCES (Zero Trust Check) ---
// All other actions require a valid Token

// A. Extract Token
$auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s+(.*)/i', $auth_header, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No token provided. Access Denied.']);
    exit;
}

// B. Verify Token (Always Verify)
$token = $matches[1];
$payload = verifyToken($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token.']);
    exit;
}

// C. Enforce Least Privilege (Role Check)
$required_roles = $access_policies[$action] ?? [];

if (empty($required_roles)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
    exit;
}

if (!in_array($payload['role'], $required_roles)) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error', 
        'message' => "Forbidden: You are a '{$payload['role']}', but this requires '" . implode(',', $required_roles) . "'."
    ]);
    exit;
}

// D. Access Granted
$messages = [
    'view' => "SUCCESS: You viewed the secure data.",
    'edit' => "SUCCESS: You edited the database.",
    'admin' => "SUCCESS: You accessed the Admin Console.",
];

echo json_encode(['status' => 'success', 'data' => $messages[$action]]);
?>