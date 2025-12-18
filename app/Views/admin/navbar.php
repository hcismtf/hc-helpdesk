<?php
$isSuperadmin = (strtolower(session('role') ?? '') === 'superadmin');
$userPermissions = session('user_permissions') ?? [];
?>
<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
        <span id="toggle-icon">&#9776;</span>
    </button>
    <div class="nav-icons">
        <?php if ($isSuperadmin || in_array('dashboard', $userPermissions)): ?>
        <a href="<?= base_url('admin/dashboard') ?>" class="<?= $active=='dashboard'?'active':'' ?>" title="Dashboard">
            <img src="<?= base_url('assets/images/dashboard_logo.svg') ?>" alt="Dashboard" width="24" height="24">
            <span class="nav-label">Dashboard</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('tickets', $userPermissions)): ?>
        <a href="<?= base_url('admin/Ticket_dashboard') ?>" class="<?= $active=='tickets'?'active':'' ?>" title="Tickets">
            <img src="<?= base_url('assets/images/tickets_logo.svg') ?>" alt="Tickets" width="24" height="24">
            <span class="nav-label">Tickets</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('system_settings', $userPermissions)): ?>
        <a href="<?= base_url('admin/system_settings') ?>" class="<?= $active=='settings'?'active':'' ?>" title="Settings">
            <img src="<?= base_url('assets/images/settings_logo.svg') ?>" alt="Settings" width="24" height="24">
            <span class="nav-label">Settings</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('user_management', $userPermissions)): ?>
        <a href="<?= base_url('admin/user_mgt') ?>" class="<?= $active=='user_mgt'?'active':'' ?>" title="User Management">
            <img src="<?= base_url('assets/images/user_logo.svg') ?>" alt="User Management" width="24" height="24">
            <span class="nav-label">User Management</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('reports', $userPermissions)): ?>
        <a href="<?= base_url('admin/report_user') ?>" class="<?= $active=='reports'?'active':'' ?>" title="Reports">
            <img src="<?= base_url('assets/images/report_logo.svg') ?>" alt="Reports" width="24" height="24">
            <span class="nav-label">Reports</span>
        </a>
        <?php endif; ?>
    </div>
    <div class="bottom-btn">
        <a href="<?= base_url('admin/logout') ?>" title="Logout">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4"></path>
                <polyline points="17 16 21 12 17 8"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span class="nav-label">Logout</span>
        </a>
    </div>
</div>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
        document.body.classList.toggle('sidebar-open');
    }
</script>