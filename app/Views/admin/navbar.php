<?php
$isSuperadmin = (strtolower(session('role') ?? '') === 'superadmin');
$userPermissions = session('user_permissions') ?? [];

// Helper check permission (bisa string tunggal atau array OR)
$canAccess = function ($required) use ($isSuperadmin, $userPermissions) {
    if ($isSuperadmin)
        return true;
    if (empty($required))
        return true;
    if (is_array($required)) {
        return !empty(array_intersect($required, $userPermissions));
    }
    return in_array($required, $userPermissions, true);
};

// Hitung open tickets untuk badge
try {
    $ticketModel = new \App\Models\TicketTransactionModel();
    $openTicketsCount = $ticketModel->where('ticket_status !=', 'closed')->countAllResults();
} catch (\Throwable $e) {
    $openTicketsCount = 0;
}

// Konfigurasi Navigasi Dinamis
$navMenuItems = [
    [
        'key' => 'dashboard',
        'title' => 'Dashboard',
        'url' => 'admin/dashboard',
        'permission' => ['dashboard', 'dev:access'],
        'badge' => 0,
        'icon' => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>'
    ],
    [
        'key' => 'tickets',
        'title' => 'Ticket List',
        'url' => 'admin/ticket_dashboard',
        'permission' => ['tickets', 'ticket:read'],
        'badge' => $openTicketsCount,
        'icon' => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"></path><line x1="13" y1="5" x2="13" y2="19" stroke-dasharray="2 2"></line>'
    ],
    [
        'key' => 'settings',
        'title' => 'System Settings',
        'url' => 'admin/system_settings',
        'permission' => ['system_settings', 'settings:read', 'faq:read', 'role:read', 'request_type:read', 'sla:read', 'permission:read'],
        'badge' => 0,
        'icon' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>'
    ],
    [
        'key' => 'user_mgt',
        'title' => 'User Management',
        'url' => 'admin/user_mgt',
        'permission' => ['user_management', 'user:read'],
        'badge' => 0,
        'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'
    ],
    [
        'key' => 'reports',
        'title' => 'Report & Export',
        'url' => 'admin/report_user',
        'permission' => ['reports', 'report:read', 'report:export'],
        'badge' => 0,
        'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>'
    ],
];
?>

<script>
    (function () {
        document.body.classList.add('preload');
        var savedState = localStorage.getItem('sidebar_open');
        var isOpen = savedState === null ? (window.innerWidth > 900) : (savedState === 'true');
        if (isOpen) {
            document.documentElement.classList.add('sidebar-is-open');
            document.documentElement.classList.remove('sidebar-is-closed');
            document.body.classList.add('sidebar-open');
        } else {
            document.documentElement.classList.add('sidebar-is-closed');
            document.documentElement.classList.remove('sidebar-is-open');
            document.body.classList.remove('sidebar-open');
        }
    })();
</script>

<div class="sidebar open" id="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-header">
        <div class="brand-wrapper" onclick="toggleSidebar()" title="Toggle sidebar">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                </svg>
            </div>
            <div class="brand-info">
                <div class="brand-title">HC Helpdesk</div>
                <div class="brand-subtitle">HC Ticketing Management</div>
            </div>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar" type="button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu Dinamis -->
    <div class="nav-icons">
        <?php foreach ($navMenuItems as $item): ?>
            <?php if ($canAccess($item['permission'])): ?>
                <a href="<?= base_url($item['url']) ?>" class="nav-item <?= ($active ?? '') === $item['key'] ? 'active' : '' ?>"
                    title="<?= esc($item['title']) ?>">
                    <div class="nav-item-content">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <?= $item['icon'] ?>
                        </svg>
                        <span class="nav-label"><?= esc($item['title']) ?></span>
                        <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                            <span class="count-badge"><?= $item['badge'] ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="active-indicator"></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Bottom Footer (Logout) -->
    <div class="sidebar-footer">
        <a href="<?= base_url('admin/logout') ?>" class="logout-btn" title="Logout Session">
            <div class="logout-icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </div>
            <span class="logout-text">Logout Session</span>
        </a>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const isCurrentlyOpen = sidebar.classList.contains('open') || document.documentElement.classList.contains('sidebar-is-open');
        const newState = !isCurrentlyOpen;

        if (newState) {
            sidebar.classList.add('open');
            document.body.classList.add('sidebar-open');
            document.documentElement.classList.add('sidebar-is-open');
            document.documentElement.classList.remove('sidebar-is-closed');
            localStorage.setItem('sidebar_open', 'true');
        } else {
            sidebar.classList.remove('open');
            document.body.classList.remove('sidebar-open');
            document.documentElement.classList.add('sidebar-is-closed');
            document.documentElement.classList.remove('sidebar-is-open');
            localStorage.setItem('sidebar_open', 'false');
        }
    }

    window.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                document.body.classList.remove('preload');
            });
        });
    });
</script>