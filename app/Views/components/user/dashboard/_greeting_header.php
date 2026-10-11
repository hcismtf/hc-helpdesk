<?php
/**
 * User Dashboard - Greeting & Action Header Component
 *
 * @var string $userName
 * @var string $division
 * @var string $currentDate
 */
?>
<section class="dashboard-greeting-header mb-5">
  <div class="greeting-left">
    <div class="greeting-title-row">
      <h1 class="greeting-heading">
        Halo, <?= esc($userName ?? 'Dimas Pratama') ?> 👋
      </h1>
      <span class="division-badge">
        <?= esc($division ?? 'Divisi Product & Design') ?>
      </span>
    </div>
    <div class="greeting-datetime">
      <i class="fa-regular fa-calendar-days mr-2"></i>
      <span><?= esc($currentDate ?? 'Kamis, 08 Oktober 2026 • 10:43 WIB') ?></span>
    </div>
  </div>

  <div class="greeting-actions">
    <a href="<?= base_url('pusat-bantuan') ?>" class="btn-dashboard-action btn-outline-guide">
      <i class="fa-regular fa-bookmark"></i>
      <span>Lihat Panduan HC</span>
    </a>
    <a href="<?= base_url('ticket/create') ?>" class="btn-dashboard-action btn-primary-new-ticket">
      <i class="fa-solid fa-plus"></i>
      <span>Buat Tiket Baru</span>
    </a>
  </div>
</section>
