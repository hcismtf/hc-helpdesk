<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
        <span id="toggle-icon">&#9776;</span>
    </button>
    <div class="nav-icons">
        <a href="<?= base_url('admin/dashboard') ?>" class="<?= $active=='dashboard'?'active':'' ?>" title="Dashboard">
            <span>📊</span>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="<?= base_url('admin/Ticket_dashboard') ?>" class="<?= $active=='tickets'?'active':'' ?>" title="Tickets">
            <span>🎫</span>
            <span class="nav-label">Tickets</span>
        </a>
        <a href="<?= base_url('admin/system_settings') ?>" class="<?= $active=='settings'?'active':'' ?>" title="Settings">
            <span>⚙️</span>
            <span class="nav-label">Settings</span>
        </a>
        <a href="#" title="Ideas">
            <span>💡</span>
            <span class="nav-label">Ideas</span>
        </a>
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