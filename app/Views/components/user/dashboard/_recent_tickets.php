<?php
/**
 * User Dashboard - Recent Tickets List Component
 *
 * @var array $tickets
 * @var int $totalActive
 * @var int $displayedCount
 */
?>
<div class="dashboard-tickets-panel card-box">
  <!-- Header: Title, Real-time status & Action Buttons -->
  <div class="panel-header">
    <div class="header-main">
      <div class="title-with-badge">
        <h2 class="panel-title">Daftar Tiket Terkini</h2>
        <span class="active-tickets-pill">
          <?= esc($totalActive ?? count($tickets)) ?> Tiket Aktif
        </span>
      </div>
      <div class="realtime-status">
        <i class="fa-solid fa-arrows-rotate mr-1"></i>
        <span>Pembaruan otomatis real-time • Terakhir diperbarui pukul <?= date('H:i') ?> WIB</span>
      </div>
    </div>
    <div class="header-actions">
      <button type="button" class="btn-action-outline">
        <i class="fa-solid fa-download mr-1"></i>
        <span>Ekspor CSV</span>
      </button>
      <a href="<?= base_url('ticket/create') ?>" class="btn-action-primary">
        <i class="fa-solid fa-plus mr-1"></i>
        <span>Buat Tiket Baru</span>
      </a>
    </div>
  </div>

  <!-- Filter Tabs -->
  <div class="ticket-filter-tabs">
    <button type="button" class="filter-tab active" data-filter="all">
      Semua Tiket <span class="tab-count"><?= esc($tabCounts['all'] ?? count($tickets)) ?></span>
    </button>
    <button type="button" class="filter-tab" data-filter="action">
      Perlu Tindakan <span class="tab-count tab-count-purple"><?= esc($tabCounts['action'] ?? 0) ?></span>
    </button>
    <button type="button" class="filter-tab" data-filter="in_progress">
      Sedang Berjalan <span class="tab-count"><?= esc($tabCounts['in_progress'] ?? 0) ?></span>
    </button>
    <button type="button" class="filter-tab" data-filter="resolved">
      Selesai <span class="tab-count tab-count-green"><?= esc($tabCounts['resolved'] ?? 0) ?></span>
    </button>
  </div>

  <!-- Search & Filter Controls -->
  <div class="ticket-search-bar">
    <div class="search-input-wrapper">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input type="text" class="input-search" id="dashboardTicketSearch" placeholder="Cari ID tiket, subjek, atau kata kunci...">
    </div>
    <!-- 1. Rich Status Filter Dropdown -->
    <?= view('components/common/_dropdown_select', [
      'id'          => 'statusFilter',
      'placeholder' => 'Status: Semua',
      'width'       => '180px',
      'options'     => [
        [
          'value'       => '',
          'label'       => 'Status: Semua',
          'dot'         => 'neutral',
          'badge'       => (string) ($tabCounts['all'] ?? count($tickets)),
          'badge_tone'  => 'neutral',
          'description' => 'Seluruh status tiket aktif',
        ],
        [
          'value'       => 'needs_action',
          'label'       => 'Butuh Respon',
          'dot'         => 'purple',
          'badge'       => (string) ($tabCounts['action'] ?? 0),
          'badge_tone'  => 'purple',
          'description' => 'Perhatian segera Anda diperlukan',
        ],
        [
          'value'       => 'in_progress',
          'label'       => 'Sedang Diproses',
          'dot'         => 'amber',
          'badge'       => (string) ($tabCounts['in_progress'] ?? 0),
          'badge_tone'  => 'amber',
          'description' => 'Dalam penanganan tim HC',
        ],
        [
          'value'       => 'open',
          'label'       => 'Tiket Terbuka',
          'dot'         => 'blue',
          'badge'       => (string) ($tabCounts['all'] ?? 0),
          'badge_tone'  => 'blue',
          'description' => 'Menunggu alokasi PIC tim terkait',
        ],
        [
          'value'       => 'resolved',
          'label'       => 'Selesai',
          'dot'         => 'green',
          'badge'       => (string) ($tabCounts['resolved'] ?? 0),
          'badge_tone'  => 'green',
          'description' => 'Tiket telah berhasil dituntaskan',
        ],
      ],
    ]) ?>

    <!-- 2. Rich Category Filter Dropdown -->
    <?= view('components/common/_dropdown_select', [
      'id'          => 'categoryFilter',
      'placeholder' => 'Kategori: Semua',
      'width'       => '190px',
      'options'     => [
        [
          'value'       => '',
          'label'       => 'Kategori: Semua',
          'icon'        => 'fa-solid fa-shapes',
          'description' => 'Seluruh kategori permasalahan',
        ],
        [
          'value'       => 'sistem',
          'label'       => 'Sistem HC & Eazy',
          'icon'        => 'fa-solid fa-mobile-screen',
          'description' => 'Aplikasi, OTP, HRIS & akun SSO',
        ],
        [
          'value'       => 'presensi',
          'label'       => 'Presensi & Kehadiran',
          'icon'        => 'fa-regular fa-clock',
          'description' => 'Koreksi absensi, mesin finger & cuti',
        ],
        [
          'value'       => 'benefit',
          'label'       => 'Benefit & Asuransi',
          'icon'        => 'fa-solid fa-shield-heart',
          'description' => 'Klaim rawat inap, BPJS & kacamata',
        ],
        [
          'value'       => 'payroll',
          'label'       => 'Payroll & Perpajakan',
          'icon'        => 'fa-solid fa-file-invoice-dollar',
          'description' => 'Slip gaji, insentif & lembur',
        ],
      ],
    ]) ?>
  </div>

  <!-- Ticket Cards List -->
  <div class="ticket-card-list">
    <?php if (empty($tickets)): ?>
      <div class="empty-state-card" style="text-align: center; padding: 48px 24px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1; margin: 16px 0;">
        <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
          <i class="fa-solid fa-ticket-simple" style="font-size: 28px; color: #94a3b8;"></i>
        </div>
        <h4 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Belum Ada Tiket yang Diajukan</h4>
        <p style="font-size: 0.875rem; color: #64748b; max-width: 420px; margin: 0 auto 20px; line-height: 1.5;">
          Anda belum memiliki pengajuan tiket aktif. Jika mengalami kendala layanan HC, buat tiket baru untuk mendapatkan bantuan cepat.
        </p>
        <a href="<?= base_url('ticket/create') ?>" class="btn-action-primary" style="display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
          <i class="fa-solid fa-plus mr-1"></i>
          <span>Buat Tiket Baru</span>
        </a>
      </div>
    <?php else: ?>
      <?php foreach ($tickets as $ticket): ?>
        <div class="ticket-item-card">
          <!-- Main Details (Left) -->
          <div class="ticket-item-body">
            <!-- Tag & SLA Row -->
            <div class="ticket-tags-row">
              <span class="tag-ticket-id"><?= esc($ticket['ticket_no']) ?></span>
              <span class="tag-category"><?= esc($ticket['category']) ?></span>
              <span class="tag-priority tag-priority-<?= esc($ticket['priority_tone']) ?>">
                <?= esc($ticket['priority']) ?>
              </span>
              <span class="tag-sla tag-sla-<?= esc($ticket['sla_tone']) ?>">
                <?php if ($ticket['sla_tone'] === 'success'): ?>
                  <span class="dot-indicator dot-green"></span>
                <?php else: ?>
                  <i class="fa-regular fa-clock mr-1"></i>
                <?php endif; ?>
                <?= esc($ticket['sla_badge']) ?>
              </span>
            </div>

            <!-- Subject & Description -->
            <h3 class="ticket-title">
              <a href="<?= base_url('my/ticket/' . esc($ticket['id'])) ?>">
                <?= esc($ticket['title']) ?>
              </a>
            </h3>
            <p class="ticket-snippet">
              <?= esc($ticket['description']) ?>
            </p>

            <!-- Footer Meta: Date & PIC -->
            <div class="ticket-meta-footer">
              <div class="meta-date">
                <i class="fa-regular fa-calendar mr-1"></i>
                <span><?= esc($ticket['created_at']) ?></span>
              </div>
              <div class="meta-pic">
                <?php if (!empty($ticket['pic_initials'])): ?>
                  <span class="pic-initials-badge"><?= esc($ticket['pic_initials']) ?></span>
                  <span class="pic-text">
                    PIC: <strong><?= esc($ticket['pic_name']) ?></strong> (<?= esc($ticket['pic_role']) ?>)
                  </span>
                <?php else: ?>
                  <span class="dot-indicator dot-gray mr-1"></span>
                  <span class="pic-text pic-unassigned">
                    PIC: <em><?= esc($ticket['pic_name']) ?></em>
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Status & Action (Right) -->
          <div class="ticket-item-action">
            <div class="status-badge-wrapper">
              <span class="status-pill status-<?= esc($ticket['status']) ?>">
                <span class="status-dot"></span>
                <?= esc($ticket['status_label']) ?>
              </span>
            </div>

            <div class="action-btn-group">
              <?php if ($ticket['action_tone'] === 'purple'): ?>
                <a href="<?= base_url('my/ticket/' . esc($ticket['id'])) ?>" class="btn-ticket-reply">
                  <span><?= esc($ticket['action_label']) ?></span>
                </a>
              <?php else: ?>
                <a href="<?= base_url('my/ticket/' . esc($ticket['id'])) ?>" class="btn-ticket-detail">
                  <span><?= esc($ticket['action_label']) ?></span>
                </a>
              <?php endif; ?>

              <button type="button" class="btn-kebab-menu" title="Menu opsi tiket" aria-label="Opsi">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Pagination Footer -->
  <div class="panel-pagination-footer">
    <div class="pagination-info">
      Menampilkan <?= esc($displayedCount ?? count($tickets)) ?> dari <strong><?= esc($totalActive ?? count($tickets)) ?></strong> tiket aktif
    </div>
    <div class="pagination-controls">
      <button type="button" class="page-btn page-arrow" disabled title="Halaman pertama">
        <i class="fa-solid fa-angles-left"></i>
      </button>
      <button type="button" class="page-btn page-arrow" disabled title="Halaman sebelumnya">
        <i class="fa-solid fa-angle-left"></i>
      </button>
      <button type="button" class="page-btn active">1</button>
      <?php if (($totalActive ?? count($tickets)) > 4): ?>
        <button type="button" class="page-btn">2</button>
        <button type="button" class="page-btn">3</button>
        <button type="button" class="page-btn">4</button>
        <button type="button" class="page-btn page-arrow" title="Halaman berikutnya">
          <i class="fa-solid fa-angle-right"></i>
        </button>
        <button type="button" class="page-btn page-arrow" title="Halaman terakhir">
          <i class="fa-solid fa-angles-right"></i>
        </button>
      <?php else: ?>
        <button type="button" class="page-btn page-arrow" disabled title="Halaman berikutnya">
          <i class="fa-solid fa-angle-right"></i>
        </button>
        <button type="button" class="page-btn page-arrow" disabled title="Halaman terakhir">
          <i class="fa-solid fa-angles-right"></i>
        </button>
      <?php endif; ?>
    </div>
  </div>
</div>
