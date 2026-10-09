<!DOCTYPE html>
<html lang="id" , data-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <title><?= esc($title ?? 'HC Helpdesk - PT Mandiri Tunas Finance') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

  <?= $this->renderSection('styles') ?>
</head>

<body class="<?= esc($bodyClass ?? '') ?>">
  <?= $this->renderSection('layout_content') ?>
  <script src="<?= base_url('js/navbar.js') ?>"></script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>