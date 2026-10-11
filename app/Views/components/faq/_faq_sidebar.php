<!-- RIGHT: Sidebar Info Cards -->
<aside class="faq-sidebar">

  <!-- Card 1: Masih Membutuhkan Bantuan? -->
  <div class="faq-help-card">
    <div class="faq-help-icon-box">
      <i class="fas fa-question-circle"></i>
    </div>
    <h3 class="faq-help-title">Masih Membutuhkan Bantuan?</h3>
    <p class="faq-help-desc">
      Belum menemukan solusi yang Anda cari di daftar FAQ? Buat tiket laporan langsung atau pantau tiket yang sedang berjalan.
    </p>
    <a href="<?= base_url('ticket/create') ?>" class="btn-sidebar-primary">
      <i class="fas fa-plus-circle"></i>
      <span>Masuk & Buat Tiket</span>
    </a>
    <a href="<?= base_url('/#lacak-tiket') ?>" class="btn-sidebar-secondary">
      <i class="fas fa-search"></i>
      <span>Lacak Status Tiket</span>
    </a>
  </div>

  <!-- Card 2: Kontak Layanan Darurat HC -->
  <div class="faq-hotline-card">
    <div class="hotline-header">
      <i class="fas fa-phone-alt"></i>
      <span>Kontak Layanan Darurat HC</span>
    </div>

    <!-- Internal Hotline -->
    <div class="hotline-box">
      <div class="hotline-box-label">
        <i class="fas fa-phone-volume"></i>
        <span>Internal Hotline Ext.</span>
      </div>
      <div class="hotline-box-val">1104 / 1105</div>
    </div>

    <!-- WhatsApp -->
    <div class="hotline-box box-wa">
      <div class="hotline-box-label">
        <i class="fab fa-whatsapp"></i>
        <span>WhatsApp Helpdesk 24/7</span>
      </div>
      <div class="hotline-box-val">
        <a href="https://wa.me/6281199887766" target="_blank" style="color: inherit; text-decoration: none;">
          +62 811-9988-7766
        </a>
      </div>
    </div>

    <!-- Hours Notice -->
    <div class="hotline-hours">
      <i class="far fa-clock" style="margin-top: 2px;"></i>
      <div>
        Jam Operasional Reguler:<br>
        <strong>Senin - Jumat: 08:00 - 17:00 WIB</strong>
      </div>
    </div>
  </div>

</aside>
