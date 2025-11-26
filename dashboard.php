<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Zero Trust Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Zero Trust Dashboard</span>
        <button onclick="logout()" class="btn btn-outline-light btn-sm">Logout</button>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12 text-center mb-4">
            <h2>Welcome!</h2>
            <p class="text-muted">Your Role: <span id="user_role" class="fw-bold text-primary">...</span></p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center shadow-sm">
                <div class="card-header">Secure Actions</div>
                <div class="card-body">
                    <p>Click a button to request data from the API. The API will verify your token every time.</p>
                    
                    <button onclick="req('view')" class="btn btn-success m-2">View Data (All)</button>
                    <button onclick="req('edit')" class="btn btn-warning m-2">Edit Data (Editor Only)</button>
                    <button onclick="req('admin')" class="btn btn-danger m-2">Admin Console (Admin Only)</button>

                    <div class="mt-4">
                        <h5>Server Response:</h5>
                        <pre id="api_response" class="bg-dark text-white p-3 rounded text-start">Waiting for action...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Check if user is logged in
    const token = localStorage.getItem('zt_token');
    const role = localStorage.getItem('zt_role');

    if (!token) {
        window.location.href = 'index.php'; // Redirect to login if no token
    } else {
        document.getElementById('user_role').textContent = role || 'Unknown';
    }

    // 2. Logout Function
    function logout() {
        localStorage.removeItem('zt_token');
        localStorage.removeItem('zt_role');
        window.location.href = 'index.php';
    }

    // 3. Request Function (The Zero Trust Handshake)
    async function req(action) {
        const out = document.getElementById('api_response');
        out.textContent = 'Verifying Token with Server...';
        out.className = 'bg-dark text-warning p-3 rounded text-start';

        try {
            const res = await fetch(`api.php?action=${action}`, {
                method: 'GET',
                headers: { 
                    'Authorization': `Bearer ${token}` // ATTACH TOKEN HERE
                }
            });
            
            const data = await res.json();
            
            // Format output
            out.textContent = JSON.stringify(data, null, 2);

            // Change color based on success/failure
            if (data.status === 'success') {
                out.className = 'bg-dark text-success p-3 rounded text-start';
            } else {
                out.className = 'bg-dark text-danger p-3 rounded text-start';
            }

        } catch (err) {
            out.textContent = 'Error connecting to API.';
            out.className = 'bg-dark text-danger p-3 rounded text-start';
        }
    }
</script>
</body>
</html>