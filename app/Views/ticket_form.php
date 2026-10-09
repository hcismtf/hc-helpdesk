<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HC Helpdesk</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/ticket_form.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/landing.css?v=' . time()) ?>">
</head>

<body>
  <?= view('components/public_navbar', ['activePage' => 'home']) ?>

  <main class="main-wrapper">
    <section class="header-section">
      <div class="header-badge">
        Formulir Resmi Pengaduan Kepegawaian
      </div>
      <h1 class="header-title">
        Ajukan Tiket Layanan Human Capital
      </h1>
      <div class="header-paragraph">
        Sampaikan kendala, pertanyaan, atau permohonan kepegawaian Anda. Tim Human Capital siap melayani dengan standar
        SLA terukur dan transparan.
      </div>
    </section>
  </main>



  <script src="<?= base_url('assets/js/ticket_form.js') ?>"></script>
  <script src="<?= base_url('assets/js/device-security-check.js') ?>"></script>
  <script>
    function showLoadingModal() {
      document.getElementById('loadingModal').style.display = 'flex';
    }

    function hideLoadingModal() {
      document.getElementById('loadingModal').style.display = 'none';
    }
  </script>
</body>

</html>