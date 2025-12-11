<!DOCTYPE html>
<html lang="id">
<head>
    <title>Developer Options</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 680px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px 28px 24px 28px;
        }
        h1 {
            font-size: 2rem;
            color: #2563eb;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        ul {
            margin: 0 0 18px 0;
            padding-left: 18px;
        }
        ul li {
            margin-bottom: 14px;
            font-size: 1.07rem;
        }
        ul ul {
            margin-top: 6px;
            margin-bottom: 0;
            padding-left: 18px;
        }
        code {
            background: #f1f5f9;
            color: #334155;
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 0.98em;
        }
        hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0 18px 0;
        }
        .note {
            color: #ef4444;
            font-weight: 500;
            font-size: 1.05rem;
        }
        .section-title {
            color: #0ea5e9;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Developer Options</h1>
        <div class="section-title">Mode Environment</div>
        <ul>
            <li>Edit file <code>.env</code> di root project:<br>
                <code>CI_ENVIRONMENT = development</code> atau <code>CI_ENVIRONMENT = production</code>
            </li>
            <li>
                Untuk mengarahkan otomatis ke halaman HCEazy ketika fitur developer aktif, tambahkan di <code>.env</code>:
                <br><code>DEVELOPER_OPTIONS_ENABLED = true</code>
            </li>
        </ul>
        <div class="section-title">=Database Config</div>
        <ul>
            <li>Edit di <code>.env</code> (hapus tanda <code>#</code> di depan baris):<br>
                <code>database.default.hostname = localhost</code><br>
                <code>database.default.database = hc_helpdesk</code><br>
                <code>database.default.username = </code><br>
                <code>database.default.password = </code><br>
                <code>database.default.DBDriver = MySQLi</code><br>
                <code>database.default.port = 3306</code>
            </li>
        </ul>
        <div class="section-title">Endpoint Testing</div>
        <ul>
            <li>
                <ul>
                    <li><code>/report/ticket-detail</code></li>
                    <li><code>/report/sla-detail</code></li>
                    <li><code>/report/sla-response-comparison</code></li>
                    <li><code>/report/sla-resolution-comparison</code></li>
                </ul>
            </li>
        </ul>
        <div class="section-title">Debugging</div>
        <ul>
            <li>Gunakan browser Developer Tools (<b>F12</b>), cek <code>writable/logs/</code>.</li>
            <li>Pastikan variable global seperti <code>BASE_URL</code> tersedia di view untuk JS eksternal.</li>
        </ul>
        <div class="section-title">Build & Dependency</div>
        <ul>
            <li>
                <code>php builds release</code> / <code>php builds development</code> lalu <code>composer update</code>.
            </li>
        </ul>
        <hr>
        <div class="note">
            Catatan: Jangan upload file <code>.env</code> ke repo. Ganti semua data sensitif jika pernah ter-push ke GitHub.
        </div>
    </div>
</body>
</html>