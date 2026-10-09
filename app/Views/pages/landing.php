<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<!-- CSS spesifik hanya untuk hero grid & carousel landing -->
<link rel="stylesheet" href="<?= base_url('css/pages/landing.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- TOP GRID: Hero & Track Form -->
<div class="top-grid">
  <div class="hero-column">
    <section class="hero-banner-card">
      <div class="hero-pill-badge">
        Sistem Layanan Mandiri Karyawan PT Mandiri Tunas Finance
      </div>
      <h1 class="hero-headline">
        Selamat Datang di portal <span class="highlight-brand">HC Helpdesk</span>
      </h1>
      <p class="hero-description">
        Satu portal terpadu untuk bantuan layanan kepegawaian, pengajuan tiket kendala, panduan SOP, dan resolusi akun
        HC Eazy.
      </p>
      <div class="hero-cta-group">
        <a href="<?= base_url('ticket/create') ?>" class="btn-hero-create-ticket">
          <span class="btn-hero-icon-box"><i class="fas fa-plus"></i></span>
          <span>Buat Tiket Bantuan</span>
          <i class="fas fa-arrow-right btn-hero-arrow"></i>
        </a>
      </div>
    </section>

    <!-- Quick Actions Row -->
    <div class="quick-actions-row">
      <!-- Card 1 -->
      <div class="quick-card">
        <div class="quick-icon-box icon-blue">
          <img src="<?= base_url('assets/icons/reset.svg') ?>" alt="Reset Sandi">
        </div>
        <div class="quick-card-info">
          <div class="quick-card-text">
            <div class="quick-card-title">Reset Sandi Aplikasi HC Eazy</div>
            <div class="quick-card-subtitle">Ganti/lupa password</div>
          </div>
          <a href="<?= base_url('ticket/create?req_type=Reset%20Password') ?>"
            class="btn-quick-action action-reset-pw">Form Reset Password</a>
        </div>
      </div>
      <!-- Card 2: Pusat Bantuan -->
      <div class="quick-card">
        <div class="quick-icon-box icon-gray">
          <img src="<?= base_url('assets/icons/shield-question.svg') ?>" alt="Pusat Bantuan">
        </div>
        <div class="quick-card-info">
          <div class="quick-card-text">
            <div class="quick-card-title">Pusat Bantuan & FAQ</div>
            <div class="quick-card-subtitle">Cari panduan kekaryawanan</div>
          </div>
          <a href="<?= base_url('pusat-bantuan') ?>" class="btn-quick-action action-faq">Buka Pusat Bantuan HC</a>
        </div>
      </div>
      <!-- Card 3: Hotline -->
      <div class="quick-card">
        <div class="quick-icon-box icon-orange">
          <img src="<?= base_url('assets/icons/call.png') ?>" alt="Hotline HC">
        </div>
        <div class="quick-card-info">
          <div class="quick-card-text">
            <div class="quick-card-title">
              Hotline Darurat HC
              <i class="fas fa-question-circle info-tip" onclick="showHotlineModal()" title="Info kontak PIC"></i>
            </div>
            <div class="quick-card-subtitle">[nomor handphone PIC Ticketing]</div>
          </div>
          <div class="badge-operational action-hotline" onclick="showHotlineModal()">Jam Operasional 08.30 - 17.30</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ASIDE: Track Column -->
  <aside class="track-column" id="lacak-tiket">
    <div class="track-ticket-card">
      <div class="track-header">
        <div class="track-icon-box">
          <img src="<?= base_url('assets/icons/global-search.svg') ?>" alt="Lacak Tiket">
        </div>
        <div>
          <h2 class="track-title">Lacak Status Tiket</h2>
          <div class="track-subtitle">Cek progres penanganan tiket secara instan</div>
        </div>
      </div>
      <p class="track-instruction">
        Sudah pernah mengajukan keluhan atau tiket bantuan sebelumnya? Masukkan nomor referensi untuk melacak status
        SLA.
      </p>

      <form id="trackTicketForm" class="track-form" onsubmit="handleTrackTicket(event)">
        <div class="track-inputs-stack">
          <div class="form-group-landing">
            <label for="track_ticket_no">Nomor Tiket <span class="required-star">*</span></label>
            <div class="input-with-icon">
              <i class="fas fa-ticket-alt input-icon"></i>
              <input type="text" id="track_ticket_no" name="ticket_no" placeholder="CTH: #HC-2025-0042" required
                autocomplete="off">
            </div>
          </div>
          <div class="form-group-landing">
            <label for="track_email">Email Pelapor <span class="label-opt">(Opsional)</span></label>
            <div class="input-with-icon">
              <i class="fas fa-envelope input-icon"></i>
              <input type="email" id="track_email" name="email" placeholder="nama@perusahaan.co.id" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="track-actions-group">
          <button type="submit" class="btn-track-submit" id="btnTrackSubmit">
            <i class="fas fa-search btn-submit-icon"></i>
            <span>Lacak Progres Penanganan</span>
          </button>
          <div class="track-no-ticket-wrap">
            <span class="no-ticket-text">Ingin melihat detail tiket?</span>
            <a href="javascript:void(0)" onclick="openUserLoginModal();" class="btn-link-create-ticket">
              <span>Masuk ke Akun</span> <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </form>
    </div>
  </aside>
</div>

<!-- SECTION: Layanan & Nilai Budaya (Carousel dsb tetap di sini) -->
<!-- ... [Konten Layanan & Corporate Values Anda] ... -->

<!-- Modal Khusus Landing Page -->
<?php /*
<?= view('components/modals/_track_result_modal') ?>
<?= view('components/modals/_hotline_modal') ?>
*/ ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Script khusus logic tracking tiket & slider carousel -->
<script src="<?= base_url('js/pages/landing.js') ?>"></script>
<?= $this->endSection() ?>