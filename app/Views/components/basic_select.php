<?php
/**
 * Reusable Basic Single Select Dropdown Component
 *
 * @var string      $name            Form input name (e.g. 'role', 'status')
 * @var string      $id              HTML element ID
 * @var string      $placeholder     Placeholder text (e.g. 'Select Option')
 * @var array       $options         List of options:
 *                                   - Associative [value => label]
 *                                   - Or array of arrays [['value' => 1, 'label' => 'Super Admin']]
 * @var string|int  $selectedValue   Pre-selected value
 * @var bool        $required        Whether field is required
 * @var string      $onChange        Optional JS callback function name / handler: "onRoleChange(value)"
 * @var string      $extraClass      Optional CSS classes on wrapper
 * @var string      $attributes      Extra HTML attributes
 */

$name          = $name ?? 'select_field';
$id            = $id ?? 'basic_select_' . uniqid();
$placeholder   = $placeholder ?? 'Select Option';
$options       = $options ?? [];
$selectedValue = $selectedValue ?? '';
$required      = $required ?? false;
$onChange      = $onChange ?? '';
$extraClass    = $extraClass ?? '';
$attributes    = $attributes ?? '';

// Normalisasi list options
$normalizedOptions = [];
foreach ($options as $key => $opt) {
    if (is_array($opt)) {
        $val = $opt['value'] ?? $opt['id'] ?? '';
        $lbl = $opt['label'] ?? $opt['name'] ?? '';
    } else {
        $val = $key;
        $lbl = $opt;
    }
    $normalizedOptions[] = [
        'value' => (string)$val,
        'label' => (string)$lbl
    ];
}

// Cari label untuk pre-selected value
$displayLabel = $placeholder;
$hasSelected = false;
foreach ($normalizedOptions as $opt) {
    if ((string)$opt['value'] === (string)$selectedValue && $selectedValue !== '') {
        $displayLabel = $opt['label'];
        $hasSelected = true;
        break;
    }
}
?>

<div class="custom-select-wrapper <?= esc($extraClass) ?>" id="select_wrapper_<?= esc($id) ?>" <?= $attributes ?>>
    <!-- Hidden actual form input -->
    <input type="hidden" 
           name="<?= esc($name) ?>" 
           id="<?= esc($id) ?>" 
           value="<?= esc($selectedValue) ?>" 
           <?= $required ? 'required' : '' ?>>

    <!-- Custom Select Trigger Button -->
    <button type="button" 
            class="custom-select-trigger <?= $hasSelected ? 'has-value' : '' ?>" 
            id="select_trigger_<?= esc($id) ?>"
            onclick="toggleCustomSelect(event, '<?= esc($id) ?>')"
            aria-haspopup="listbox"
            aria-expanded="false">
        <span class="select-trigger-text" id="select_label_<?= esc($id) ?>"><?= esc($displayLabel) ?></span>
        <svg class="select-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>

    <!-- Dropdown Options Menu -->
    <div class="custom-select-menu" id="select_menu_<?= esc($id) ?>" role="listbox">
        <?php if ($placeholder !== ''): ?>
            <div class="custom-select-option option-placeholder <?= !$hasSelected ? 'is-selected' : '' ?>" 
                 onclick="selectCustomOption('<?= esc($id) ?>', '', '<?= esc(addslashes($placeholder)) ?>', '<?= esc($onChange) ?>')"
                 role="option">
                <span><?= esc($placeholder) ?></span>
            </div>
        <?php endif; ?>

        <?php foreach ($normalizedOptions as $opt): ?>
            <?php $isSelected = ((string)$opt['value'] === (string)$selectedValue && $selectedValue !== ''); ?>
            <div class="custom-select-option <?= $isSelected ? 'is-selected' : '' ?>" 
                 data-value="<?= esc($opt['value']) ?>"
                 onclick="selectCustomOption('<?= esc($id) ?>', '<?= esc(addslashes($opt['value'])) ?>', '<?= esc(addslashes($opt['label'])) ?>', '<?= esc($onChange) ?>')"
                 role="option">
                <span><?= esc($opt['label']) ?></span>
                <svg class="select-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* Encapsulated Basic Select Styles */
.custom-select-wrapper {
    position: relative;
    width: 100%;
    user-select: none;
    box-sizing: border-box;
}
.custom-select-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    font-size: 13.5px;
    font-weight: 500;
    color: #94a3b8;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    cursor: pointer;
    text-align: left;
    outline: none;
    transition: all 0.15s ease;
    box-sizing: border-box;
    font-family: inherit;
}
.custom-select-trigger.has-value {
    color: #0f172a;
    font-weight: 600;
}
.custom-select-trigger:hover,
.custom-select-wrapper.is-open .custom-select-trigger {
    border-color: #94a3b8;
    background: #ffffff;
}
.custom-select-wrapper.is-open .custom-select-trigger {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
.select-chevron-icon {
    color: #64748b;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}
.custom-select-wrapper.is-open .select-chevron-icon {
    transform: rotate(180deg);
    color: #2563eb;
}
.custom-select-menu {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 100%;
    max-height: 240px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
    padding: 6px;
    z-index: 99999;
    box-sizing: border-box;
}
.custom-select-wrapper.is-open .custom-select-menu {
    display: block !important;
}
.custom-select-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.12s ease;
}
.custom-select-option:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.custom-select-option.is-selected {
    background: #eff6ff;
    color: #2563eb;
    font-weight: 600;
}
.select-check-icon {
    opacity: 0;
    color: #2563eb;
    transition: opacity 0.1s ease;
}
.custom-select-option.is-selected .select-check-icon {
    opacity: 1;
}
.custom-select-option.option-placeholder {
    color: #94a3b8;
}
</style>

<script>
if (typeof window.toggleCustomSelect !== 'function') {
    window.toggleCustomSelect = function(e, id) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        var wrapper = document.getElementById('select_wrapper_' + id);
        if (!wrapper) return;
        var isOpen = wrapper.classList.contains('is-open');
        
        // Close other custom selects
        document.querySelectorAll('.custom-select-wrapper.is-open').forEach(function(w) {
            if (w !== wrapper) {
                w.classList.remove('is-open');
                var trigger = w.querySelector('.custom-select-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        });

        if (isOpen) {
            wrapper.classList.remove('is-open');
            var trigger = wrapper.querySelector('.custom-select-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        } else {
            wrapper.classList.add('is-open');
            var trigger = wrapper.querySelector('.custom-select-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
        }
    };

    window.selectCustomOption = function(id, value, label, onChangeCallback) {
        var input = document.getElementById(id);
        var labelEl = document.getElementById('select_label_' + id);
        var trigger = document.getElementById('select_trigger_' + id);
        var wrapper = document.getElementById('select_wrapper_' + id);

        if (input) {
            input.value = value;
            var event = new Event('change', { bubbles: true });
            input.dispatchEvent(event);
        }
        if (labelEl) labelEl.textContent = label;
        if (trigger) {
            if (value !== '') {
                trigger.classList.add('has-value');
            } else {
                trigger.classList.remove('has-value');
            }
        }
        if (wrapper) {
            wrapper.classList.remove('is-open');
            wrapper.querySelectorAll('.custom-select-option').forEach(function(opt) {
                opt.classList.remove('is-selected');
                if (opt.getAttribute('data-value') === value || (value === '' && opt.classList.contains('option-placeholder'))) {
                    opt.classList.add('is-selected');
                }
            });
        }
        if (onChangeCallback && typeof window[onChangeCallback] === 'function') {
            window[onChangeCallback](value);
        }
    };

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            document.querySelectorAll('.custom-select-wrapper.is-open').forEach(function(w) {
                w.classList.remove('is-open');
                var trigger = w.querySelector('.custom-select-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            });
        }
    });
}
</script>
