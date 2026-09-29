<?php
/**
 * Reusable Admin Header Component
 *
 * @var string $breadcrumbRoot   (optional, default: 'Helpdesk Admin')
 * @var string $breadcrumbActive (optional, default: 'Analytics Overview')
 * @var string $pageTitle        (optional, default: 'Dashboard')
 * @var bool   $showCreateTicket (optional, default: true)
 * @var bool   $showNotif        (optional, default: true)
 */
$breadcrumbRoot   = $breadcrumbRoot ?? 'Helpdesk Admin';
$breadcrumbActive = $breadcrumbActive ?? 'Analytics Overview';
$pageTitle        = $pageTitle ?? 'Dashboard';
$showCreateTicket = $showCreateTicket ?? true;
$showNotif        = $showNotif ?? true;

$username = session('username') ?? 'Admin';
$userId   = session('user_id');

// Ambil role: prioritas dari prop $role / $roleName -> session('role_name') -> session('role') -> database lookup
$resolvedRole = $roleName ?? $role ?? session('role_name') ?? session('role');

if ((!$resolvedRole || strtolower($resolvedRole) === 'user') && $userId) {
    try {
        $db = \Config\Database::connect();
        $roleRow = $db->table('role_detail')
            ->join('role', 'role.id = role_detail.role_id')
            ->where('role_detail.user_id', $userId)
            ->select('role.name as role_name')
            ->get()
            ->getRowArray();
        if (!empty($roleRow['role_name'])) {
            $resolvedRole = $roleRow['role_name'];
        }
    } catch (\Throwable $e) {
        // Fallback jika DB query gagal
    }
}

$displayRole = strtoupper($resolvedRole ?? 'Superadmin');
$initials    = strtoupper(substr(trim($username), 0, 2));
?>
<!-- Modern Top Header Component -->
<div class="dashboard-top-header">
    <div class="header-left-col">
        <div class="dashboard-breadcrumb">
            <span class="breadcrumb-root"><?= esc($breadcrumbRoot) ?></span>
            <span class="breadcrumb-divider">/</span>
            <span class="breadcrumb-active"><?= esc($breadcrumbActive) ?></span>
        </div>
        <h1 class="dashboard-title"><?= esc($pageTitle) ?></h1>
    </div>

    <div class="header-right-col">
        <?php if ($showCreateTicket): ?>
        <!-- Create Ticket Button -->
        <button type="button" class="btn-create-ticket" onclick="openCreateTicketModal()" title="Create New Ticket">
            <span class="btn-plus">+</span>
            <span>Create Ticket</span>
        </button>
        <?php endif; ?>

        <?php if ($showCreateTicket && $showNotif): ?>
        <div class="header-divider"></div>
        <?php endif; ?>

        <?php if ($showNotif): ?>
        <!-- Notification Bell -->
        <div class="header-action-icon notif-bell" title="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notif-indicator"></span>
        </div>
        <?php endif; ?>

        <!-- User Profile Badge & Dropdown -->
        <div class="header-user-dropdown-container">
            <div class="header-user-pill" onclick="toggleUserDropdown(event)" id="userProfileDropdownTrigger" title="Account Menu">
                <div class="user-avatar"><?= esc($initials) ?></div>
                <div class="user-info-text">
                    <span class="user-fullname"><?= esc($username) ?></span>
                    <span class="user-role-tag"><?= esc($displayRole) ?></span>
                </div>
                <svg class="chevron-icon" id="userDropdownChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            <!-- Dropdown Menu -->
            <div class="header-dropdown-menu" id="userProfileDropdownMenu">
                <div class="dropdown-header-info">
                    <div class="dropdown-avatar-large"><?= esc($initials) ?></div>
                    <div class="dropdown-user-text">
                        <span class="dropdown-name"><?= esc($username) ?></span>
                        <span class="dropdown-role"><?= esc($displayRole) ?></span>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('admin/dashboard') ?>" class="dropdown-menu-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span>System Settings</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('admin/logout') ?>" class="dropdown-menu-item item-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUserDropdown(event) {
    if (event) event.stopPropagation();
    var menu = document.getElementById('userProfileDropdownMenu');
    var chevron = document.getElementById('userDropdownChevron');
    if (!menu) return;
    var isOpen = menu.classList.contains('show');
    if (isOpen) {
        menu.classList.remove('show');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    } else {
        menu.classList.add('show');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    }
}

document.addEventListener('click', function(e) {
    var container = document.querySelector('.header-user-dropdown-container');
    var menu = document.getElementById('userProfileDropdownMenu');
    var chevron = document.getElementById('userDropdownChevron');
    if (container && !container.contains(e.target) && menu && menu.classList.contains('show')) {
        menu.classList.remove('show');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
});
</script>

<?php if ($showCreateTicket): ?>
    <?= view('components/create_ticket_modal') ?>
<?php endif; ?>
