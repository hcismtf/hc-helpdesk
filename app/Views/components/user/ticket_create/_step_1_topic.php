<?php
/**
 * Step 1: Pilih Topik Layanan & Prioritas
 *
 * @var array $requestTypes Daftar request types dari database
 */

// Default categories untuk visualisasi intuitif HC
$defaultCategories = [
    [
        'id'          => 'presensi',
        'key'         => 'Presensi & Kehadiran',
        'icon'        => 'fa-solid fa-calendar-check',
        'color'       => '#2563EB',
        'title'       => 'Presensi & Kehadiran',
        'description' => 'Koreksi absensi, lupa tap ID, pengajuan cuti mendesak, dinas luar & lembur.',
        'sla'         => 'SLA: &le; 8 Jam'
    ],
    [
        'id'          => 'payroll',
        'key'         => 'Payroll & Penggajian',
        'icon'        => 'fa-solid fa-money-bill-wave',
        'color'       => '#059669',
        'title'       => 'Payroll & Penggajian',
        'description' => 'Pertanyaan slip gaji, koreksi tunjangan/insentif, potongan PPh 21 & rekening.',
        'sla'         => 'SLA: &le; 12 Jam'
    ],
    [
        'id'          => 'asuransi',
        'key'         => 'Klaim Asuransi & Benefit',
        'icon'        => 'fa-solid fa-shield-heart',
        'color'       => '#D97706',
        'title'       => 'Klaim Asuransi & Benefit',
        'description' => 'Klaim rawat inap/jalan, penambahan tanggungan, kartu asuransi & BPJS.',
        'sla'         => 'SLA: &le; 24 Jam'
    ],
    [
        'id'          => 'fasilitas',
        'key'         => 'Akun & Fasilitas HC',
        'icon'        => 'fa-solid fa-laptop-code',
        'color'       => '#7C3AED',
        'title'       => 'Akun & Fasilitas HC',
        'description' => 'Kendala akun HCIS/ESS, pergantian device presensi (IMEI), reset akses.',
        'sla'         => 'SLA: &le; 4 Jam'
    ],
    [
        'id'          => 'administrasi',
        'key'         => 'Administrasi & Surat HC',
        'icon'        => 'fa-solid fa-file-signature',
        'color'       => '#0284C7',
        'title'       => 'Administrasi & Surat HC',
        'description' => 'Permohonan surat keterangan kerja, rekomendasi visa, paklaring & update KK.',
        'sla'         => 'SLA: &le; 24 Jam'
    ],
    [
        'id'          => 'lainnya',
        'key'         => 'Lainnya / Umum',
        'icon'        => 'fa-solid fa-circle-question',
        'color'       => '#475569',
        'title'       => 'Lainnya / Umum',
        'description' => 'Pertanyaan umum kepegawaian atau kendala di luar kategori di atas.',
        'sla'         => 'SLA: &le; 12 Jam'
    ]
];

// Siapkan kategori utama dari DB atau fallback
$categoryList = [];
if (!empty($categories)) {
    foreach ($categories as $c) {
        $categoryList[] = [
            'id'          => is_array($c) ? $c['id'] : $c->id,
            'title'       => is_array($c) ? $c['name'] : $c->getName(),
            'icon'        => is_array($c) ? ($c['icon'] ?? 'fa-solid fa-folder') : $c->getIcon(),
            'color'       => is_array($c) ? ($c['color'] ?? '#2563EB') : $c->getColor(),
            'description' => is_array($c) ? ($c['description'] ?? '') : ($c->getDescription() ?? ''),
        ];
    }
}
if (empty($categoryList)) {
    $categoryList = $defaultCategories;
}

// Request types dari DB terorganisir per category_id
$rtByCategory = [];
$firstRtVal = '';
if (!empty($requestTypes)) {
    foreach ($requestTypes as $rt) {
        $cId = is_array($rt) ? ($rt['category_id'] ?? '') : ($rt->getCategoryId() ?? '');
        $name = is_array($rt) ? $rt['name'] : $rt->name;
        $id = is_array($rt) ? $rt['id'] : $rt->id;
        $desc = is_array($rt) ? ($rt['description'] ?? '') : ($rt->description ?? '');
        $badge = is_array($rt) ? ($rt['badge_tag'] ?? '') : '';

        if (!$firstRtVal) {
            $firstRtVal = $name;
        }

        if (!isset($rtByCategory[$cId])) {
            $rtByCategory[$cId] = [];
        }
        $rtByCategory[$cId][] = [
            'id'    => $id,
            'name'  => $name,
            'desc'  => $desc,
            'badge' => $badge
        ];
    }
}

$firstCatId = $categoryList[0]['id'] ?? '';
$firstCatTitle = $categoryList[0]['title'] ?? '';
$initReqTypeVal = $firstRtVal ?: $firstCatTitle;
?>

<div class="ticket-step-panel is-active" id="stepPanel1">
  <div class="panel-card">
    <div class="panel-header">
      <span class="panel-step-badge">Langkah 1 dari 3</span>
      <h2 class="panel-title">Pilih Topik Layanan & Kategori</h2>
      <p class="panel-desc">
        Tentukan kategori topik utama Human Capital, lalu pilih jenis layanan pengaduan yang relevan.
      </p>
    </div>

    <!-- Hidden Input untuk Request Type dan Category -->
    <input type="hidden" name="category_id" id="input_category_id" value="<?= esc($firstCatId) ?>">
    <input type="hidden" name="req_type" id="input_req_type" value="<?= esc($initReqTypeVal) ?>" required>

    <!-- 1. Grid Kategori Utama (Main Category - Gambar 3) -->
    <div class="mb-4">
      <label class="label is-size-6 mb-2">1. Pilih Pilar / Topik Kategori Utama <span class="has-text-danger">*</span></label>
      <div class="category-cards-grid">
        <?php foreach ($categoryList as $index => $cat): ?>
          <?php $isSelected = ($index === 0); ?>
          <div class="category-card <?= $isSelected ? 'is-selected' : '' ?>"
               data-category-id="<?= esc($cat['id']) ?>"
               data-category-title="<?= esc($cat['title']) ?>"
               onclick="TicketWizard.onMainCategorySelected(this, '<?= esc(addslashes($cat['id'])) ?>', '<?= esc(addslashes($cat['title'])) ?>')">
            <div class="category-card-header">
              <div class="category-icon-wrapper" style="--icon-color: <?= esc($cat['color']) ?>">
                <i class="<?= esc($cat['icon']) ?>"></i>
              </div>
              <div class="category-radio-indicator">
                <i class="fa-solid fa-check"></i>
              </div>
            </div>
            <h4 class="category-card-title"><?= esc($cat['title']) ?></h4>
            <p class="category-card-desc"><?= esc($cat['description']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- 2. Sub Category / Jenis Layanan Spesifik (Gambar 1 & Gambar 2) -->
    <div class="field mb-5" id="subCategorySection">
      <label class="label is-size-6 mb-2">
        2. Pilih Jenis Layanan / Sub-Kategori <span class="has-text-danger">*</span>
      </label>
      <p class="is-size-7 has-text-grey mb-3">
        Daftar layanan spesifik yang tersedia pada pilar kategori yang Anda pilih di atas.
      </p>
      <div class="control">
        <div class="select is-fullwidth custom-bulma-select" style="max-width: 600px;">
          <select id="select_db_req_type" onchange="TicketWizard.onSelectDropdownType(this.value)">
            <?php foreach ($requestTypes as $rt): ?>
              <?php 
                $rtCatId = is_array($rt) ? ($rt['category_id'] ?? '') : ($rt->getCategoryId() ?? '');
                $rtName = is_array($rt) ? $rt['name'] : $rt->name;
                $rtDesc = is_array($rt) ? ($rt['description'] ?? '') : ($rt->description ?? '');
                $rtBadge = is_array($rt) ? ($rt['badge_tag'] ?? '') : '';
              ?>
              <option value="<?= esc($rtName) ?>" data-cat-id="<?= esc($rtCatId) ?>">
                <?= esc($rtName) ?><?= !empty($rtBadge) ? ' [' . esc($rtBadge) . ']' : '' ?><?= !empty($rtDesc) ? ' — ' . esc($rtDesc) : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Pilihan Prioritas Pengaduan -->
    <div class="priority-selection-section mb-6">
      <label class="label is-size-6 mb-2">
        Tingkat Prioritas Penanganan <span class="has-text-danger">*</span>
      </label>
      <p class="is-size-7 has-text-grey mb-3">
        Tingkat prioritas menentukan target Service Level Agreement (SLA) waktu penyelesaian tiket Anda.
      </p>

      <div class="priority-cards-grid">
        <!-- Low / Normal -->
        <label class="priority-card" for="priority_low">
          <input type="radio" name="ticket_priority" id="priority_low" value="low">
          <div class="priority-card-inner">
            <div class="priority-badge-row">
              <span class="priority-pill pill-low">Biasa / Low</span>
              <span class="priority-sla">Target: 48 Jam</span>
            </div>
            <h5 class="priority-name">Pertanyaan Umum</h5>
            <p class="priority-detail">Konsultasi informasi umum, tidak menghambat pekerjaan mendesak.</p>
          </div>
        </label>

        <!-- Medium (Default) -->
        <label class="priority-card is-selected" for="priority_medium">
          <input type="radio" name="ticket_priority" id="priority_medium" value="medium" checked>
          <div class="priority-card-inner">
            <div class="priority-badge-row">
              <span class="priority-pill pill-medium">Standar / Medium</span>
              <span class="priority-sla">Target: 24 Jam</span>
            </div>
            <h5 class="priority-name">Kendala Rutin</h5>
            <p class="priority-detail">Koreksi absensi harian, update data kepegawaian standar.</p>
          </div>
        </label>

        <!-- High / Urgent -->
        <label class="priority-card" for="priority_high">
          <input type="radio" name="ticket_priority" id="priority_high" value="high">
          <div class="priority-card-inner">
            <div class="priority-badge-row">
              <span class="priority-pill pill-high">Mendesak / High</span>
              <span class="priority-sla">Target: &le; 4 Jam</span>
            </div>
            <h5 class="priority-name">Kritis / Cut-off</h5>
            <p class="priority-detail">Mendekati batas cut-off payroll, rawat inap darurat RS.</p>
          </div>
        </label>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="panel-action-footer">
      <div class="action-footer-left">
        <span class="text-hint">
          <i class="fa-solid fa-circle-info mr-1 text-info"></i> Lanjutkan untuk mengisi data identitas & kronologi.
        </span>
      </div>
      <div class="action-footer-right">
        <button type="button" class="button is-primary is-medium btn-step-next" onclick="TicketWizard.nextStep(1)">
          <span>Lanjut ke Detail Masalah & Validasi</span>
          <span class="icon is-small ml-2">
            <i class="fa-solid fa-arrow-right"></i>
          </span>
        </button>
      </div>
    </div>

  </div>
</div>
