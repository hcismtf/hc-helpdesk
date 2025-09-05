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
            <span>📊</span>
            <span class="nav-label">Dashboard</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('tickets', $userPermissions)): ?>
        <a href="<?= base_url('admin/Ticket_dashboard') ?>" class="<?= $active=='tickets'?'active':'' ?>" title="Tickets">
            <span>🎫</span>
            <span class="nav-label">Tickets</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('system_settings', $userPermissions)): ?>
        <a href="<?= base_url('admin/system_settings') ?>" class="<?= $active=='settings'?'active':'' ?>" title="Settings">
            <span>⚙️</span>
            <span class="nav-label">Settings</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('user_management', $userPermissions)): ?>
        <a href="<?= base_url('admin/user_mgt') ?>" class="<?= $active=='user_mgt'?'active':'' ?>" title="User Management">
            <span>👤</span>
            <span class="nav-label">User Management</span>
        </a>
        <?php endif; ?>
        <?php if ($isSuperadmin || in_array('reports', $userPermissions)): ?>
        <a href="<?= base_url('admin/report_user') ?>" class="<?= $active=='reports'?'active':'' ?>" title="Reports">
            <span>📝</span>
            <span class="nav-label">Reports</span>
        </a>
        <?php endif; ?>
    </div>
    <div class="bottom-btn">
        <a href="<?= base_url('admin/logout') ?>" title="Logout">
            <span>🔒</span>
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