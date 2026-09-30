<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>403 Forbidden</title>

    <style>
        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #555;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0.5rem;
            color: #d9534f;
        }
        .wrap {
            max-width: 600px;
            margin: 5rem auto;
            padding: 2.5rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        p {
            margin-top: 1rem;
            font-size: 1.05rem;
            line-height: 1.5;
        }
        .btn-back {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.6rem 1.2rem;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
        }
        .btn-back:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>403 Forbidden</h1>
    <p><?= esc($message ?? 'Anda tidak memiliki hak akses untuk mengakses halaman ini.') ?></p>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn-back">Kembali ke Dashboard</a>
</div>
</body>
</html>
