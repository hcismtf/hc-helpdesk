<?php
/**
 * System Settings Navigation Bar Component
 *
 * @var array $counts
 */
$counts = $counts ?? [];
?>
<div class="settings-nav-bar" id="settingsNavBar">
    <button type="button" class="settings-nav-btn active" onclick="switchSettingsTab('roles')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>
        <span>Roles & Permissions</span>
        <span class="nav-counter-pill"><?= (int)($counts['roles'] ?? 0) ?> Roles</span>
    </button>

    <button type="button" class="settings-nav-btn" onclick="switchSettingsTab('sla')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <span>SLA Policies</span>
        <span class="nav-counter-pill"><?= (int)($counts['slas'] ?? 0) ?> Tiers</span>
    </button>

    <button type="button" class="settings-nav-btn" onclick="switchSettingsTab('request_type')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
        </svg>
        <span>Request Type</span>
        <span class="nav-counter-pill"><?= (int)($counts['requestTypes'] ?? 0) ?> Types</span>
    </button>

    <button type="button" class="settings-nav-btn" onclick="switchSettingsTab('faq')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span>FAQ Management</span>
        <span class="nav-counter-pill"><?= (int)($counts['faqs'] ?? 0) ?> Articles</span>
    </button>
</div>
