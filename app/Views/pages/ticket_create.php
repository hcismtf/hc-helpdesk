<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="ticket-create-page-container">

  <!-- 1. Header Banner -->
  <?= view('components/user/ticket_create/_header_banner') ?>

  <!-- 2. Wizard Stepper (3 Langkah Sesuai Desain) -->
  <?= view('components/user/ticket_create/_wizard_stepper') ?>

  <!-- 3. Form Grid Utama: Wizard Steps (Kiri) & Sidebar Panduan (Kanan) -->
  <div class="ticket-create-main-grid">

    <!-- Kolom Kiri: Form Wizard Interaktif -->
    <div class="ticket-wizard-column">
      <form id="formTicketCreate" enctype="multipart/form-data" method="POST" action="<?= base_url('ticket/store') ?>" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="is_ajax" value="1">

        <!-- Panel Langkah 1: Kategori & Prioritas -->
        <?= view('components/user/ticket_create/_step_1_topic', [
          'categories'   => $categories ?? [],
          'requestTypes' => $requestTypes ?? []
        ]) ?>

        <!-- Panel Langkah 2: Identitas & Detail Kendala -->
        <?= view('components/user/ticket_create/_step_2_details', [
          'currentUser' => $currentUser ?? null
        ]) ?>

        <!-- Panel Langkah 3: Lampiran & Konfirmasi Akhir -->
        <?= view('components/user/ticket_create/_step_3_attachment_confirm') ?>
      </form>
    </div>

    <!-- Kolom Kanan: Sidebar Panduan SLA & Bantuan -->
    <div class="ticket-sidebar-column">
      <?= view('components/user/ticket_create/_ticket_sidebar') ?>
    </div>

  </div>

</div>

<!-- 4. Modal Konfirmasi Sukses (Toast mengambang disediakan secara global oleh layout) -->
<?= view('components/modals/_ticket_success_modal') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/modules/ticket-create.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
