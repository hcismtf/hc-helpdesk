<?php
$isSuperadmin = (strtolower(session('role') ?? '') === 'superadmin');
$userPermissions = session('user_permissions') ?? [];
try {
    $ticketModel = new \App\Models\TicketModel();
    $openTicketsCount = $ticketModel->where('ticket_status !=', 'closed')->countAllResults();
} catch (\Exception $e) {
    $openTicketsCount = 0;
}
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

    <!-- Navigation Menu -->
    <div class="nav-icons">
        <?php if ($isSuperadmin || in_array('dashboard', $userPermissions)): ?>
            <a href="<?= base_url('admin/dashboard') ?>" class="nav-item <?= $active == 'dashboard' ? 'active' : '' ?>"
                title="Dashboard">
                <div class="nav-item-content">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    <span class="nav-label">Dashboard</span>
                </div>
                <span class="active-indicator"></span>
            </a>
        <?php endif; ?>

        <?php if ($isSuperadmin || in_array('tickets', $userPermissions)): ?>
            <a href="<?= base_url('admin/Ticket_dashboard') ?>" class="nav-item <?= $active == 'tickets' ? 'active' : '' ?>"
                title="Ticket List">
                <div class="nav-item-content">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z">
                        </path>
                        <line x1="13" y1="5" x2="13" y2="19" stroke-dasharray="2 2"></line>
                    </svg>
                    <span class="nav-label">Ticket List</span>
                    <?php if ($openTicketsCount > 0): ?>
                        <span class="count-badge"><?= $openTicketsCount ?></span>
                    <?php endif; ?>
                </div>
                <span class="active-indicator"></span>
            </a>
        <?php endif; ?>

        <?php if ($isSuperadmin || in_array('system_settings', $userPermissions)): ?>
            <a href="<?= base_url('admin/system_settings') ?>" class="nav-item <?= $active == 'settings' ? 'active' : '' ?>"
                title="System Settings">
                <div class="nav-item-content">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                        </path>
                    </svg>
                    <span class="nav-label">System Settings</span>
                </div>
                <span class="active-indicator"></span>
            </a>
        <?php endif; ?>

        <?php if ($isSuperadmin || in_array('user_management', $userPermissions)): ?>
            <a href="<?= base_url('admin/user_mgt') ?>" class="nav-item <?= $active == 'user_mgt' ? 'active' : '' ?>"
                title="User Management">
                <div class="nav-item-content">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span class="nav-label">User Management</span>
                </div>
                <span class="active-indicator"></span>
            </a>
        <?php endif; ?>

        <?php if ($isSuperadmin || in_array('reports', $userPermissions)): ?>
            <a href="<?= base_url('admin/report_user') ?>" class="nav-item <?= $active == 'reports' ? 'active' : '' ?>"
                title="Report & Export">
                <div class="nav-item-content">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span class="nav-label">Report & Export</span>
                </div>
                <span class="active-indicator"></span>
            </a>
        <?php endif; ?>
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

    // Remove preload transition blocker after initial frame render
    window.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                document.body.classList.remove('preload');
            });
        });
    });
</script>