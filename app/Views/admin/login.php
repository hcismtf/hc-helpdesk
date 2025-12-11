<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - HC Helpdesk</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/login.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f6fa;
            font-family: Montserrat, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }
        
        .login-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-background {
            position: absolute;
            bottom: 0;
            left: 170px;
            width: 100%;
            height: 100%;
            z-index: 1;
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
        }
        
        .login-background img {
            width: 600px;
            height: auto;
            max-height: 100%;
            object-fit: contain;
        }
        
        .login-container {
            position: relative;
            z-index: 10;
        }

        @media (max-width: 768px) {
            .login-background {
                left: 0;
            }

            .login-background img {
                width: 350px;
            }

            .login-container {
                margin-right: 20px;
            }
        }

        @media (max-width: 480px) {
            .login-background img {
                width: 250px;
            }

            .login-container {
                margin-right: 30px;
            }
        }
    </style>
</head>
<body>
    
    <div class="login-wrapper">
        <!-- Logo Background -->
        <div class="login-background">
            <img src="<?= base_url('assets/images/login_logo.svg') ?>" alt="login-logo">
        </div>

        <!-- Login Box -->
        <div class="login-container">
            <h2 class="login-title">HC Helpdesk Admin</h2>

            <form action="<?= base_url('admin/authenticate') ?>" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username">
                </div>

                <div class="input-group password-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password">
                    <span class="toggle-password" onclick="togglePassword()" style="position: absolute; right: 12px; top: 70%; transform: translateY(-50%); cursor: pointer; color: #666;">
                        <i id="eye-icon" class="fas fa-eye"></i>
                    </span>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <a href="<?= base_url('/') ?>" class="btn-back">Back to loading page</a>
        </div>
    </div>

    <!-- Include Global Error Modal Component -->
    <?php include(APPPATH . 'Views/components/error_confirm.php'); ?>

    <!-- Show error if exists -->
    <?php if (session('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showGlobalError('<?= htmlspecialchars(session('error')) ?>');
        });
    </script>
    <?php endif; ?>

    <script>
    function togglePassword() {
        const input = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");
        if (input.type === "password") {
            input.type = "text";
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
    </script>

</body>
</html>
