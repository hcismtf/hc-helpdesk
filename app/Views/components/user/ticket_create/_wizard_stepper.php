<?php
/**
 * Wizard Stepper Component
 * Sesuai mockup: 3 langkah interaktif dengan state Completed (Checkmark), Active (Badge Sedang Diisi), dan Pending.
 */
?>
<div class="ticket-wizard-stepper-container mb-5">
  <div class="stepper-track">

    <!-- LANGKAH 1 -->
    <div class="stepper-item is-active" id="stepIndicator1" onclick="TicketWizard.goToStep(1)">
      <div class="stepper-icon-box">
        <span class="stepper-icon" id="stepIcon1">1</span>
      </div>
      <div class="stepper-content">
        <div class="stepper-meta-row">
          <span class="stepper-label" id="stepLabel1">LANGKAH 1 (AKTIF)</span>
          <span class="stepper-active-badge" id="stepBadge1">Sedang Diisi</span>
        </div>
        <h3 class="stepper-title">Pilih Topik Layanan</h3>
        <p class="stepper-subtitle">Kategori & Jalur Penanganan</p>
      </div>
    </div>

    <!-- LANGKAH 2 -->
    <div class="stepper-item is-pending" id="stepIndicator2" onclick="TicketWizard.goToStep(2)">
      <div class="stepper-icon-box">
        <span class="stepper-icon" id="stepIcon2">2</span>
      </div>
      <div class="stepper-content">
        <div class="stepper-meta-row">
          <span class="stepper-label" id="stepLabel2">LANGKAH 2</span>
          <span class="stepper-active-badge" id="stepBadge2" style="display: none;">Sedang Diisi</span>
        </div>
        <h3 class="stepper-title">Detail Masalah & Validasi</h3>
        <p class="stepper-subtitle">Data NIK, subjek & kronologi</p>
      </div>
    </div>

    <!-- LANGKAH 3 -->
    <div class="stepper-item is-pending" id="stepIndicator3" onclick="TicketWizard.goToStep(3)">
      <div class="stepper-icon-box">
        <span class="stepper-icon" id="stepIcon3">3</span>
      </div>
      <div class="stepper-content">
        <div class="stepper-meta-row">
          <span class="stepper-label" id="stepLabel3">LANGKAH 3</span>
          <span class="stepper-active-badge" id="stepBadge3" style="display: none;">Sedang Diisi</span>
        </div>
        <h3 class="stepper-title">Lampiran & Konfirmasi</h3>
        <p class="stepper-subtitle">Estimasi waktu: ~2 menit</p>
      </div>
    </div>

  </div>
</div>
