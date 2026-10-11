<?= $this->extend('layouts/public') ?>


<?= $this->section('content') ?>

<!-- TOP GRID: Left Hero & Quick Actions, Right Track Ticket -->
<div class="top-grid">

  <!-- LEFT COLUMN -->
  <div class="hero-column">
    <?= view('components/landing/_hero_banner') ?>
    <?= view('components/landing/_quick_actions') ?>
  </div>

  <!-- RIGHT COLUMN -->
  <?= view('components/landing/_track_ticket_card') ?>

</div>

<!-- SECTION: Layanan & Bantuan Kepegawaian (Carousel) -->
<?= view('components/landing/_services_carousel', ['popularServices' => $popularServices ?? []]) ?>

<!-- SECTION: Nilai Budaya Perusahaan MTF -->
<?= view('components/landing/_corporate_values') ?>

<!-- Modals Khusus Landing Page -->
<?= view('components/modals/_track_result_modal') ?>
<?= view('components/modals/_hotline_modal') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/modules/landing.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>