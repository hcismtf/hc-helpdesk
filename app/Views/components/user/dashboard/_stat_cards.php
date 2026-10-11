<?php
/**
 * User Dashboard - Stat / Metric Cards Component
 *
 * @var array $metrics
 */
?>
<section class="dashboard-stats-grid mb-5">
  <!-- Card 1: Tiket Terbuka -->
  <div class="stat-card stat-theme-blue">
    <div class="stat-icon-wrapper">
      <i class="fa-regular fa-envelope"></i>
    </div>
    <div class="stat-content">
      <span class="stat-label">TIKET TERBUKA</span>
      <div class="stat-number-row">
        <span class="stat-number"><?= esc($metrics['open']['count'] ?? 1) ?></span>
        <span class="stat-subtext">
          <span class="stat-unit">antrean</span>
          <span class="stat-dot dot-blue"></span>
          <span class="stat-desc">Menunggu alokasi</span>
        </span>
      </div>
    </div>
  </div>

  <!-- Card 2: Sedang Diproses -->
  <div class="stat-card stat-theme-amber">
    <div class="stat-icon-wrapper">
      <i class="fa-regular fa-clock"></i>
    </div>
    <div class="stat-content">
      <span class="stat-label">SEDANG DIPROSES</span>
      <div class="stat-number-row">
        <span class="stat-number"><?= esc($metrics['in_progress']['count'] ?? 2) ?></span>
        <span class="stat-subtext">
          <span class="stat-unit">berjalan</span>
          <span class="stat-dot dot-amber"></span>
          <span class="stat-desc">Dalam penanganan</span>
        </span>
      </div>
    </div>
  </div>

  <!-- Card 3: Butuh Respon -->
  <div class="stat-card stat-theme-purple">
    <div class="stat-icon-wrapper">
      <i class="fa-regular fa-comment-dots"></i>
    </div>
    <div class="stat-content">
      <div class="stat-label-row">
        <span class="stat-dot dot-purple header-dot"></span>
        <span class="stat-label text-purple">BUTUH RESPON</span>
      </div>
      <div class="stat-number-row">
        <span class="stat-number"><?= esc($metrics['action_needed']['count'] ?? 1) ?></span>
        <span class="stat-subtext">
          <span class="stat-unit">tindakan</span>
          <span class="stat-dot dot-purple"></span>
          <span class="stat-desc">Perhatian Segera</span>
        </span>
      </div>
    </div>
  </div>

  <!-- Card 4: Tiket Selesai -->
  <div class="stat-card stat-theme-green">
    <div class="stat-icon-wrapper">
      <i class="fa-regular fa-circle-check"></i>
    </div>
    <div class="stat-content">
      <span class="stat-label">TIKET SELESAI</span>
      <div class="stat-number-row">
        <span class="stat-number"><?= esc($metrics['resolved']['count'] ?? 12) ?></span>
        <span class="stat-subtext">
          <span class="stat-unit">riwayat</span>
          <span class="stat-dot dot-green"></span>
          <span class="stat-desc">CSAT 4.9/5 • 100% SLA</span>
        </span>
      </div>
    </div>
  </div>
</section>
