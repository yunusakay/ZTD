<?php
// config.php - Shared Configuration and Security Functions

// Security Constants
define('SECRET_KEY', 'your_super_secret_key_12345');
define('TOKEN_EXPIRY', 3600); // 1 hour

// Mock User Database
$mock_users = [
    'analyst' => ['password' => 'pass123', 'role' => 'viewer'],
    'admin'   => ['password' => 'pass456', 'role' => 'editor'],
];

// Zero Trust Policies (Least Privilege)
$access_policies = [
    'view' => ['viewer', 'editor'],
    'edit' => ['editor'],
    'admin' => ['admin_only'],
];

// --- Security Functions ---

function createMockToken(array $payload): string {
    $header = base64_encode(json_encode(['alg' => 'MOCK', 'typ' => 'ZT_TOKEN']));
    $body = base64_encode(json_encode($payload));
    $signature = hash('sha256', "$header.$body." . SECRET_KEY);
    return "$header.$body.$signature";
}

function verifyToken(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    list($header_b64, $body_b64, $signature_check) = $parts;
    $expected_signature = hash('sha256', "$header_b64.$body_b64." . SECRET_KEY);
    
    if ($signature_check !== $expected_signature) return null;

    $payload = json_decode(base64_decode($body_b64), true);
    if (!isset($payload['exp']) || $payload['exp'] < time()) return null;

    return $payload;
}
?>