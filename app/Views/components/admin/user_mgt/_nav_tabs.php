<?php
/**
 * User Management Navigation Tabs Component
 *
 * @var int $total
 * @var int $allUserTotal
 */
$total = $total ?? 0;
$allUserTotal = $allUserTotal ?? 0;
?>
<div class="settings-nav-bar">
    <button type="button" class="settings-nav-btn active" id="btn-tab-users-role" onclick="switchUserTab('users_role')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <span>Users with Role (Admin / Staff)</span>
        <span class="nav-counter-pill" id="counter-users-role"><?= (int)$total ?></span>
    </button>
    <button type="button" class="settings-nav-btn" id="btn-tab-all-users" onclick="switchUserTab('all_users')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
        </svg>
        <span>Seluruh User & Karyawan (Database)</span>
        <span class="nav-counter-pill" id="counter-all-users"><?= (int)$allUserTotal ?></span>
    </button>
</div>
