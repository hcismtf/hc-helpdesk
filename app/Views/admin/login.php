<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - HC Helpdesk</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/login.css') ?>"> 
</head>
<body style="background:#f5f6fa; font-family: Montserrat, sans-serif;">
    
    <div class="login-container">
        <h2 class="login-title">HC Helpdesk Admin</h2>

        <form action="<?= base_url('admin/authenticate') ?>" method="post">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="input-group password-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()" style="margin-top: 10px; cursor:pointer;">
                    <span id="eye-icon">👁️</span>
                </span>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <a href="<?= base_url('/') ?>" class="btn-back">Back to loading page</a>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");
        if (input.type === "password") {
            input.type = "text";
            eyeIcon.textContent = "👁️‍🗨️"; // Mata terbuka
        } else {
            input.type = "password";
            eyeIcon.textContent = "👁️"; // Mata tertutup
        }
    }
    </script>

</body>
</html>
