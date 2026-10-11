<?php
/**
 * Global Rich Dropdown Select Component
 *
 * @var string      $id              ID elemen (wajib, contoh: 'statusFilter')
 * @var string      $name            Nama input form (opsional, default: $id)
 * @var string      $placeholder     Label default saat belum ada yang terpilih (contoh: 'Pilih Opsi')
 * @var array       $options         Daftar opsi (array asosiatif / array of arrays):
 *                                   - value: string
 *                                   - label: string
 *                                   - dot: string (opsional: 'purple', 'amber', 'blue', 'green', 'neutral')
 *                                   - icon: string (opsional fontawesome class: 'fa-solid fa-mobile-screen')
 *                                   - badge: string|int (opsional counter / pill)
 *                                   - badge_tone: string ('purple', 'amber', 'green', 'blue')
 *                                   - description: string (teks informasi tambahan di bawah label)
 * @var string|int  $selectedValue   Nilai terpilih saat ini
 * @var string      $width           Lebar komponen (opsional, contoh: '180px', '220px', '100%')
 * @var string      $onChange        Nama fungsi JS callback opsional: "onFilterChange(val)"
 * @var string      $extraClass      Class tambahan untuk wrapper
 */

$id            = $id ?? 'dropdown_' . uniqid();
$name          = $name ?? $id;
$placeholder   = $placeholder ?? 'Pilih Opsi';
$options       = $options ?? [];
$selectedValue = (string)($selectedValue ?? '');
$width         = $width ?? 'auto';
$onChange      = $onChange ?? '';
$extraClass    = $extraClass ?? '';

// Normalisasi opsi
$normalized = [];
$selectedOption = null;

foreach ($options as $key => $opt) {
    if (is_array($opt)) {
        $val  = (string)($opt['value'] ?? $key);
        $lbl  = (string)($opt['label'] ?? $val);
        $dot  = $opt['dot'] ?? null;
        $icon = $opt['icon'] ?? null;
        $bdg  = $opt['badge'] ?? null;
        $tone = $opt['badge_tone'] ?? 'neutral';
        $desc = $opt['description'] ?? null;
    } else {
        $val  = (string)$key;
        $lbl  = (string)$opt;
        $dot  = null;
        $icon = null;
        $bdg  = null;
        $tone = 'neutral';
        $desc = null;
    }

    $item = [
        'value'       => $val,
        'label'       => $lbl,
        'dot'         => $dot,
        'icon'        => $icon,
        'badge'       => $bdg,
        'badge_tone'  => $tone,
        'description' => $desc,
    ];

    $normalized[] = $item;

    if ($val === $selectedValue && $selectedOption === null) {
        $selectedOption = $item;
    }
}

// Jika belum ada terpilih, gunakan opsi pertama jika nilainya cocok atau gunakan placeholder
if ($selectedOption === null && !empty($normalized) && $selectedValue !== '') {
    $selectedOption = $normalized[0];
}

$displayLabel = $selectedOption['label'] ?? $placeholder;
$displayDot   = $selectedOption['dot'] ?? null;
$displayIcon  = $selectedOption['icon'] ?? null;
?>

<div class="rich-dropdown-wrapper <?= esc($extraClass) ?>" 
     id="rich_dropdown_<?= esc($id) ?>" 
     style="<?= $width !== 'auto' ? 'width: ' . esc($width) . ';' : '' ?>"
     data-dropdown-id="<?= esc($id) ?>">

  <!-- Hidden input asli untuk form submit & JS change event listener -->
  <input type="hidden" 
         name="<?= esc($name) ?>" 
         id="<?= esc($id) ?>" 
         value="<?= esc($selectedValue) ?>">

  <!-- Trigger Button (Rounded SM) -->
  <button type="button" 
          class="rich-dropdown-trigger" 
          id="trigger_<?= esc($id) ?>"
          onclick="RichDropdown.toggle('<?= esc($id) ?>', event)"
          aria-haspopup="listbox" 
          aria-expanded="false">
    
    <div class="trigger-label-group">
      <?php if (!empty($displayDot)): ?>
        <span class="trigger-dot dot-<?= esc($displayDot) ?>"></span>
      <?php elseif (!empty($displayIcon)): ?>
        <i class="<?= esc($displayIcon) ?> trigger-icon"></i>
      <?php endif; ?>
      <span class="trigger-text" id="label_<?= esc($id) ?>"><?= esc($displayLabel) ?></span>
    </div>

    <i class="fa-solid fa-chevron-down trigger-chevron"></i>
  </button>

  <!-- Dropdown Menu (Rounded SM) -->
  <div class="rich-dropdown-menu" id="menu_<?= esc($id) ?>" role="listbox">
    <?php foreach ($normalized as $item): ?>
      <?php 
        $isSelected = ($item['value'] === $selectedValue);
      ?>
      <div class="rich-dropdown-item <?= $isSelected ? 'is-selected' : '' ?>" 
           role="option"
           aria-selected="<?= $isSelected ? 'true' : 'false' ?>"
           data-value="<?= esc($item['value']) ?>"
           data-label="<?= esc($item['label']) ?>"
           data-dot="<?= esc($item['dot'] ?? '') ?>"
           data-icon="<?= esc($item['icon'] ?? '') ?>"
           onclick="RichDropdown.select('<?= esc($id) ?>', '<?= esc(addslashes($item['value'])) ?>', '<?= esc(addslashes($item['label'])) ?>', '<?= esc($item['dot'] ?? '') ?>', '<?= esc($item['icon'] ?? '') ?>', '<?= esc($onChange) ?>', event)">

        <!-- Left: Dot Indicator or Category Icon -->
        <div class="item-visual-indicator">
          <?php if (!empty($item['dot'])): ?>
            <span class="item-dot dot-<?= esc($item['dot']) ?>"></span>
          <?php elseif (!empty($item['icon'])): ?>
            <i class="<?= esc($item['icon']) ?> item-icon"></i>
          <?php else: ?>
            <span class="item-dot dot-neutral"></span>
          <?php endif; ?>
        </div>

        <!-- Center: Informative Text & Description -->
        <div class="item-text-group">
          <div class="item-primary-row">
            <span class="item-title"><?= esc($item['label']) ?></span>
            <?php if (!empty($item['badge'])): ?>
              <span class="item-pill-badge badge-tone-<?= esc($item['badge_tone']) ?>">
                <?= esc($item['badge']) ?>
              </span>
            <?php endif; ?>
          </div>
          <?php if (!empty($item['description'])): ?>
            <span class="item-description"><?= esc($item['description']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Right: Active Checkmark Indicator -->
        <div class="item-check-indicator">
          <i class="fa-solid fa-check"></i>
        </div>

      </div>
    <?php endforeach; ?>
  </div>
</div>
