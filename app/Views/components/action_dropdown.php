<?php
/**
 * Reusable Action Dropdown Menu Component
 *
 * @var string $id           Unique identifier for the dropdown
 * @var array  $items        List of dropdown items:
 *                           [
 *                             'label' => 'Edit Role',
 *                             'icon'  => '<svg>...</svg>', // optional SVG string or icon name
 *                             'onClick' => 'openEditModal(...)', // optional JS click handler
 *                             'href'  => 'admin/tickets/edit/1', // optional link href
 *                             'class' => 'danger' // optional 'danger' | 'warning' | 'default'
 *                             'attributes' => 'data-id="1"' // optional extra HTML attributes
 *                           ]
 * @var string $align        'right' | 'left' (default: 'right')
 * @var string $buttonTitle  Title/tooltip for the 3-dots trigger button (default: 'Actions')
 * @var string $extraClass   Additional CSS class on wrapper
 */

$id          = $id ?? 'dropdown_' . uniqid();
$items       = $items ?? [];
$align       = $align ?? 'right';
$buttonTitle = $buttonTitle ?? 'Actions';
$extraClass  = $extraClass ?? '';
?>

<div class="action-dropdown-wrapper <?= esc($extraClass) ?>" id="wrapper_<?= esc($id) ?>">
    <button type="button" 
            class="btn-action-trigger" 
            onclick="toggleActionDropdown(event, '<?= esc($id) ?>')" 
            title="<?= esc($buttonTitle) ?>"
            aria-haspopup="true"
            aria-expanded="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="1"></circle>
            <circle cx="12" cy="5" r="1"></circle>
            <circle cx="12" cy="19" r="1"></circle>
        </svg>
    </button>
    
    <div class="action-dropdown-menu align-<?= esc($align) ?>" id="menu_<?= esc($id) ?>">
        <?php foreach ($items as $item): ?>
            <?php 
                $isDanger = isset($item['class']) && $item['class'] === 'danger';
                $itemClass = 'action-dropdown-item' . ($isDanger ? ' item-danger' : '');
                $tag = !empty($item['href']) ? 'a' : 'button';
                $attrs = $item['attributes'] ?? '';
            ?>
            <<?= $tag ?> 
                <?= !empty($item['href']) ? 'href="' . esc($item['href']) . '"' : 'type="button"' ?>
                class="<?= $itemClass ?>"
                <?php if (!empty($item['onClick'])): ?>onclick="<?= $item['onClick'] ?>; closeAllActionDropdowns();"<?php endif; ?>
                <?= $attrs ?>>
                <?php if (!empty($item['icon'])): ?>
                    <span class="dropdown-item-icon"><?= $item['icon'] ?></span>
                <?php endif; ?>
                <span class="dropdown-item-label"><?= esc($item['label'] ?? '') ?></span>
            </<?= $tag ?>>
        <?php endforeach; ?>
    </div>
</div>
