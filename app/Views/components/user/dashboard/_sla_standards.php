<?php
/**
 * User Dashboard - SLA Standards Widget Component
 *
 * @var array $slaStandards
 */
?>
<div class="dashboard-widget card-box mb-5">
  <!-- Widget Header -->
  <div class="widget-header">
    <div class="widget-title-group">
      <div class="widget-icon-box">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <h3 class="widget-title">Standar SLA Layanan HC</h3>
    </div>
    <a href="<?= base_url('pusat-bantuan#sla') ?>" class="widget-header-link">
      Lihat Semua
    </a>
  </div>

  <!-- SLA Items List -->
  <div class="sla-service-list">
    <?php foreach ($slaStandards as $sla): ?>
      <div class="sla-service-item">
        <div class="sla-item-top">
          <h4 class="sla-item-title"><?= esc($sla['title']) ?></h4>
          <span class="sla-badge sla-badge-<?= esc($sla['badge_tone']) ?>">
            <?= esc($sla['badge']) ?>
          </span>
        </div>
        <p class="sla-item-desc"><?= esc($sla['description']) ?></p>
        <div class="sla-item-bottom">
          <div class="sla-achievement">
            <span class="sla-status-dot"></span>
            <span><?= esc($sla['rate']) ?></span>
          </div>
          <a href="<?= esc($sla['link']) ?>" class="btn-sla-apply">
            + Ajukan
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
