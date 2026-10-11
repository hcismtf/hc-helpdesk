<?php
/**
 * Modern Clean Admin Sidebar Navigation with Categorized Sections
 *
 * Menu Structure:
 * - Dashboard
 *
 * [TICKETS]
 * -- All Ticket
 * -- My Ticket (Assigned to logged-in user)
 *
 * [SYSTEM SETTINGS]
 * -- Roles Category (Permission Modules)
 * -- User Roles
 * -- SLA Policies
 * -- Request Category (Request Types)
 * -- FAQ Management
 * -- User Management
 *
 * [ANALYTICS & EXPORT]
 * -- Report
 *
 * Bottom Section:
 * - Logout Button
 * - User Profile Card (Initials, Full Name, Position/Role)
 *
 * @var string|null $active    Active main menu key (dashboard, tickets, settings, user_mgt, reports)
 * @var string|null $subActive Active sub-item key (all_tickets, my_tickets, roles, sla, request_type, faq, etc.)
 */

$cleanRole = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]/', '', (string)(session('role') ?? ''))));
$isSuperadmin = ($cleanRole === 'superadmin');
$userPermissions = session('user_permissions') ?? [];

// Helper check permission (string atau array)
$canAccess = function ($required) use ($isSuperadmin, $userPermissions) {
    if ($isSuperadmin) return true;
    if (empty($required)) return true;
    if (is_array($required)) {
        return !empty(array_intersect($required, $userPermissions));
    }
    return in_array($required, $userPermissions, true);
};

// Hitung open tickets & my tickets untuk badge
$openTicketsCount = 0;
$myTicketsCount = 0;
$currentUserId = session('user_id') ?? '';
$currentUserEmpNo = session('employee_no') ?? session('username') ?? '';

try {
    $ticketModel = new \App\Models\TicketModel();
    $openTicketsCount = $ticketModel->whereNotIn('status', ['closed', 'done', 'resolved'])->countAllResults();

    if (!empty($currentUserId) || !empty($currentUserEmpNo)) {
        $myBuilder = $ticketModel->builder();
        $myBuilder->whereNotIn('status', ['closed', 'done', 'resolved']);
        $myBuilder->groupStart();
        if ($currentUserId) {
            $myBuilder->where('pic_helpdesk_id', $currentUserId)
                      ->orWhere('pic_dev_ba_id', $currentUserId);
        }
        if ($currentUserEmpNo) {
            $myBuilder->orWhere('pic_helpdesk_id', $currentUserEmpNo)
                      ->orWhere('pic_dev_ba_id', $currentUserEmpNo);
        }
        $myBuilder->groupEnd();
        $myTicketsCount = $myBuilder->countAllResults();
    }
} catch (\Throwable $e) {
    // Fail-safe
}

// Data pengguna yang sedang login
$currentUserName     = session('name') ?? session('username') ?? 'Admin MTF';
$currentUserPosition = session('position') ?? session('role_name') ?? session('role') ?? 'Staff Helpdesk';
$currentUserInitials = strtoupper(substr(trim($currentUserName), 0, 2));

// Penentuan active state
$currentActive    = $active ?? 'dashboard';
$currentSubActive = $subActive ?? '';

// Definisi Kategori Menu Flat dengan Section Header & Line Separator
$menuSections = [
    [
        'header' => null, // Section utama tanpa header khusus
        'items'  => [
            [
                'key'        => 'dashboard',
                'title'      => 'Dashboard',
                'url'        => 'admin/dashboard',
                'permission' => ['dashboard', 'dev:access'],
                'icon'       => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>',
            ],
        ]
    ],
    [
        'header' => 'TICKET MANAGEMENT',
        'items'  => [
            [
                'key'        => 'all_tickets',
                'title'      => 'All Ticket',
                'url'        => 'admin/ticket_dashboard',
                'permission' => ['tickets', 'ticket:read'],
                'badge'      => $openTicketsCount,
                'icon'       => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z"></path><line x1="13" y1="5" x2="13" y2="19" stroke-dasharray="2 2"></line>',
            ],
            [
                'key'        => 'my_tickets',
                'title'      => 'My Ticket',
                'url'        => 'admin/ticket_dashboard?scope=my',
                'permission' => ['tickets', 'ticket:read'],
                'badge'      => $myTicketsCount,
                'icon'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline>',
            ],
        ]
    ],
    [
        'header' => 'SYSTEM SETTINGS',
        'items'  => [
            [
                'key'        => 'roles_category',
                'title'      => 'Roles Category',
                'url'        => 'admin/system_settings/roles',
                'permission' => ['role:read', 'permission:read', 'settings:read'],
                'icon'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
            ],
            [
                'key'        => 'sla_policies',
                'title'      => 'SLA Policies',
                'url'        => 'admin/system_settings/sla',
                'permission' => ['sla:read', 'settings:read'],
                'icon'       => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
            ],
            [
                'key'        => 'request_category',
                'title'      => 'Request Category',
                'url'        => 'admin/system_settings/request_types',
                'permission' => ['request_type:read', 'settings:read'],
                'icon'       => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>',
            ],
            [
                'key'        => 'faq_management',
                'title'      => 'FAQ Management',
                'url'        => 'admin/system_settings/faqs',
                'permission' => ['faq:read', 'settings:read'],
                'icon'       => '<circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line>',
            ],
            [
                'key'        => 'user_mgt',
                'title'      => 'User Management',
                'url'        => 'admin/system_settings/users',
                'permission' => ['user_management', 'user:read'],
                'icon'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line>',
            ],
        ]
    ],
    [
        'header' => 'REPORT & EXPORT',
        'items'  => [
            [
                'key'        => 'reports',
                'title'      => 'Report',
                'url'        => 'admin/report_user',
                'permission' => ['reports', 'report:read', 'report:export'],
                'icon'       => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>',
            ],
        ]
    ]
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

    <!-- Navigation Menu Categorized List -->
    <div class="nav-icons" id="sidebarNavIcons">
        <?php foreach ($menuSections as $section): ?>
            <!-- Filter items yang user punya izin akses -->
            <?php 
                $visibleItems = array_filter($section['items'], function($it) use ($canAccess) {
                    return $canAccess($it['permission']);
                });
            ?>
            <?php if (!empty($visibleItems)): ?>
                
                <?php if (!empty($section['header'])): ?>
                    <!-- Category Header with Underline -->
                    <div class="nav-section-divider">
                        <span class="nav-section-title"><?= esc($section['header']) ?></span>
                        <div class="nav-section-line"></div>
                    </div>
                <?php endif; ?>

                <?php foreach ($visibleItems as $item): ?>
                    <?php 
                        $isActive = false;
                        if (!empty($currentSubActive)) {
                            // When specific subActive is provided, only match that exact sub item!
                            if ($currentSubActive === $item['key']) {
                                $isActive = true;
                            }
                        } else {
                            if ($item['key'] === 'dashboard' && $currentActive === 'dashboard') {
                                $isActive = true;
                            } elseif ($item['key'] === 'all_tickets' && $currentActive === 'tickets' && empty($_GET['scope'])) {
                                $isActive = true;
                            } elseif ($item['key'] === 'my_tickets' && $currentActive === 'tickets' && ($_GET['scope'] ?? '') === 'my') {
                                $isActive = true;
                            } elseif ($item['key'] === 'user_mgt' && $currentActive === 'user_mgt') {
                                $isActive = true;
                            } elseif ($item['key'] === 'reports' && $currentActive === 'reports') {
                                $isActive = true;
                            } elseif ($currentActive === 'settings') {
                                $reqTab = $_GET['tab'] ?? 'roles';
                                if ($item['key'] === 'roles_category' && $reqTab === 'roles') $isActive = true;
                                elseif ($item['key'] === 'user_roles' && $reqTab === 'roles') $isActive = false;
                                elseif ($item['key'] === 'sla_policies' && $reqTab === 'sla') $isActive = true;
                                elseif ($item['key'] === 'request_category' && $reqTab === 'request_type') $isActive = true;
                                elseif ($item['key'] === 'faq_management' && $reqTab === 'faq') $isActive = true;
                            }
                        }
                    ?>
                    <a href="<?= base_url($item['url']) ?>" 
                       class="nav-item <?= $isActive ? 'active' : '' ?>"
                       title="<?= esc($item['title']) ?>">
                        <div class="nav-item-content">
                            <svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                <?php endforeach; ?>

            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Bottom Footer (Logout Button + User Info) -->
    <div class="sidebar-footer">
        <!-- 1. Logout Action Button -->
        <a href="<?= base_url('admin/logout') ?>" class="logout-btn" title="Logout Session">
            <div class="logout-icon-wrapper">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </div>
            <span class="logout-text">Logout Session</span>
        </a>

        <!-- 2. Logged-in User Profile Card -->
        <div class="sidebar-user-card" title="<?= esc($currentUserName) ?> (<?= esc($currentUserPosition) ?>)">
            <div class="user-avatar-badge"><?= esc($currentUserInitials) ?></div>
            <div class="user-details-col">
                <span class="user-name-text"><?= esc($currentUserName) ?></span>
                <span class="user-position-text"><?= esc($currentUserPosition) ?></span>
            </div>
        </div>
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