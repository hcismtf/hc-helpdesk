<!-- 3 Quick Action Cards Row -->
<div class="quick-actions-row">

  <!-- Quick Card 1: Reset Sandi HC Eazy -->
  <div class="quick-card">
    <div class="quick-icon-box icon-blue">
      <img src="<?= base_url('assets/icons/reset.svg') ?>" alt="Reset Sandi">
    </div>
    <div class="quick-card-info">
      <div class="quick-card-text">
        <div class="quick-card-title">Reset Sandi Aplikasi HC Eazy</div>
        <div class="quick-card-subtitle">Ganti/lupa password</div>
      </div>
      <a href="<?= base_url('ticket/create?req_type=Reset%20Password') ?>" class="btn-quick-action action-reset-pw">
        Form Reset Password
      </a>
    </div>
  </div>

  <!-- Quick Card 2: Pusat Bantuan & FAQ -->
  <div class="quick-card">
    <div class="quick-icon-box icon-gray">
      <img src="<?= base_url('assets/icons/shield-question.svg') ?>" alt="Pusat Bantuan">
    </div>
    <div class="quick-card-info">
      <div class="quick-card-text">
        <div class="quick-card-title">Pusat Bantuan & FAQ</div>
        <div class="quick-card-subtitle">Cari panduan kekaryawanan</div>
      </div>
      <a href="<?= base_url('pusat-bantuan') ?>" class="btn-quick-action action-faq">
        Buka Pusat Bantuan HC
      </a>
    </div>
  </div>

  <!-- Quick Card 3: Hotline Darurat HC -->
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
      <div class="badge-operational action-hotline" onclick="showHotlineModal()">
        Jam Operasional 08.30 - 17.30
      </div>
    </div>
  </div>

</div>
