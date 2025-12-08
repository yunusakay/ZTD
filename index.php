<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zero Trust Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card shadow-lg" style="width: 400px;">
    <div class="card-header bg-primary text-white text-center">
        <h4>Zero Trust Login</h4>
    </div>
    <div class="card-body">
        <form id="login_form">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="analyst" required>
                <div class="form-text">Try: 'analyst' or 'admin'</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" value="pass123" required>
                <div class="form-text">Pass: 'pass123' or 'pass456'</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
        <div id="error_msg" class="mt-3 text-danger text-center fw-bold"></div>
    </div>
</div>

<script>
    document.getElementById('login_form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const errorDiv = document.getElementById('error_msg');
        errorDiv.textContent = 'Authenticating...';

        try {
            const formData = new FormData(e.target);
            const res = await fetch('api.php?action=login', {
                method: 'POST',
                body: new URLSearchParams(formData)
            });
            const data = await res.json();

            if (data.status === 'success') {
                localStorage.setItem('zt_token', data.token);
                localStorage.setItem('zt_role', data.role);
                window.location.href = 'dashboard.php';
            } else {
                errorDiv.textContent = data.message;
            }
        } catch (err) {
            console.error(err);
            errorDiv.textContent = 'Connection Error: Ensure api.php is in the same directory.';
        }
    });
</script>
</body>
</html>