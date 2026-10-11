<?php
/**
 * User Dashboard - Quick Help & Support Widget Component
 *
 * @var array $quickLinks
 */
?>
<div class="dashboard-widget card-box">
  <!-- Widget Header -->
  <div class="widget-header">
    <div class="widget-title-group">
      <div class="widget-icon-box icon-box-green">
        <i class="fa-solid fa-book-open"></i>
      </div>
      <h3 class="widget-title">Pusat Bantuan Cepat</h3>
    </div>
  </div>

  <!-- Search FAQ Input -->
  <div class="widget-search-box mb-3">
    <i class="fa-solid fa-magnifying-glass widget-search-icon"></i>
    <input type="text" class="widget-search-input" id="quickFaqSearch" placeholder="Cari solusi instan (FAQ)...">
  </div>

  <!-- Quick Articles List -->
  <div class="quick-article-list mb-4">
    <?php foreach ($quickLinks as $link): ?>
      <a href="<?= esc($link['url']) ?>" class="quick-article-item">
        <div class="article-info">
          <i class="<?= esc($link['icon']) ?> article-icon"></i>
          <span class="article-title"><?= esc($link['title']) ?></span>
        </div>
        <i class="fa-solid fa-chevron-right article-chevron"></i>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Urgent Support Highlight Card -->
  <div class="urgent-support-banner">
    <div class="urgent-icon-wrapper">
      <i class="fa-solid fa-phone"></i>
    </div>
    <div class="urgent-text-content">
      <span class="urgent-heading">BUTUH BANTUAN MENDESAK?</span>
      <p class="urgent-desc">
        Hubungi Hotline HC Internal di <strong>Ext. 4410</strong> atau WhatsApp resmi HC Officer di jam operasional (08:00 - 17:00 WIB).
      </p>
    </div>
  </div>
</div>
