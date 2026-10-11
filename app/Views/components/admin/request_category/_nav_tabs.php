<?php
/**
 * Request Category Navigation Tabs Component
 * Switcher between Sub Category (Request Types) and Main Category (Topik HC)
 *
 * @var int $total
 * @var int $mainCategoryTotal
 */
$total = $total ?? 0;
$mainCategoryTotal = $mainCategoryTotal ?? 0;
?>
<div class="settings-nav-bar">
    <button type="button" class="settings-nav-btn active" id="btn-tab-sub-category" onclick="switchCategoryTab('sub_category')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
        </svg>
        <span>Sub Category (Request Types)</span>
        <span class="nav-counter-pill" id="counter-sub-cat"><?= (int)$total ?></span>
    </button>
    <button type="button" class="settings-nav-btn" id="btn-tab-main-category" onclick="switchCategoryTab('main_category')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Main Category (Topik HC)</span>
        <span class="nav-counter-pill" id="counter-main-cat"><?= (int)$mainCategoryTotal ?></span>
    </button>
</div>
