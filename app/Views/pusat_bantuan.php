<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/ticket_form.css') ?>">

    <title>HC Helpdesk</title>
</head>

<body>
    <div class="container py-4">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 class="fw-bold">HC Helpdesk</h3>
                <p class="text-muted">Get support and find answers to common question</p>
            </div>
            <div>
                <a href="<?= base_url(relativePath: '/') ?>" class="admin-login-btn">
                    Submit Ticket HC
                </a>
            </div>
        </div>



        <!-- Message -->
        <div class="mt-5 px-4 mb-5" style="border-radius:16px;box-shadow: 0 4px 12px rgba(0,0,0,0.1);padding: 20px;margin: auto;">
            <div style="display: flex; width: 100%;">
                <div style="margin-top:30px;">
                    <h4 class="fw-bold">Pusat Bantuan HC Helpdesk</h3>
                    <p class="text-muted"><?= date_indo(date('Y-m-d')) ?></p>
                </div>
                <div style="width: 100%; max-width: 180px; margin-left: auto; margin-right: 25px;">
                    <img src="<?= base_url(relativePath: 'assets/images/warning-shield.svg') ?>"
                        alt="warning-shield"
                        style="width: 100%; height: auto;">
                </div>
            </div>

            <div class="px-2 pr-5" style="text-align: justify;">
                <p>Pelanggan Yth.,</p>
                <p><?= isset($message['message']) ? esc($message['message']) : 'No message available'; ?></p><br>
                <p>Hormat kami, PT Mandiri Tunas Finance</p>
                <p>Mandiri Tunas Finance berizin dan diawasi oleh Otoritas Jasa Keuangan</p>
            </div>
        </div>

        <div class="logo-section">
        <img src="<?= base_url('assets/images/logo-perwira.png') ?>" alt="logo-perwira">
        </div>
    </div>
    <script src="<?= base_url('assets/js/device-security-check.js') ?>"></script>
</body>

</html>