<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - HC Helpdesk</title>
    <link rel="stylesheet" href="<?= base_url('css/ticket_form.css') ?>">
    <style>
        /* Container utama untuk form login */
        .login-container {
            width: 400px;
            height: 500px;
            flex-shrink: 0;
            border-radius: 20px;
            background: #FFF;
            box-shadow: 0 0 50px 10px rgba(0, 0, 0, 0.40);
            margin: 80px auto; /* agar form ada di tengah */
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }

        /* Title HC Helpdesk Admin */
        .login-title {
            color: #000;
            font-family: Montserrat, sans-serif;
            font-size: 28px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-bottom: 40px;
            text-align: center;
        }

        /* Group untuk label + input */
        .input-group {
            display: flex;
            width: 314px;
            height: 62px;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 10px;
            flex-shrink: 0;
            margin-bottom: 20px;
            margin-right : 43px;
            margin-left : 43px;
        }

        .input-group label {
            font-family: Montserrat, sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #000;
        }

        .input-group input {
            display: flex;
            width: 314px;
            padding: 10px 10px 10px 15px;
            align-items: center;
            gap: 10px;
            border-radius: 30px;
            border: 1px solid #82868C; /* Grey-400 */
            background: #FFF;
            font-size: 14px;
            font-family: Montserrat, sans-serif;
        }


        /* Tombol Login */
        .btn-login {
            display: flex;
            width: 314px;
            padding: 15px 20px;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-radius: 40px;
            border: 1px solid #A8A8A8;
            background: #FFF;
            font-family: Montserrat, sans-serif;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            margin-right : 43px;
            margin-left : 43px;
            font-family: Montserrat, sans-serif;
            font-size: 13px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            color: var(--Grey-400, #82868C);
            text-align: center;
        }

        /* Tombol Back */
        .btn-back {
            display: flex;
            width: 314px;
            padding: 15px 20px;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-radius: 40px;
            background: #2940D3;
            color: #FFF;
            font-family: Montserrat, sans-serif;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            text-decoration: none;
            text-align: center;
        }

    </style>
</head>
<body style="background:#f5f6fa; font-family: Montserrat, sans-serif;">
    
    <div class="login-container">
        <h2 class="login-title">HC Helpdesk Admin</h2>

        <form action="<?= base_url('admin/authenticate') ?>" method="post">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <a href="<?= base_url('/') ?>" class="btn-back">Back to loading page</a>
    </div>

</body>
</html>
