<?php
/**
 * User Management Page Orchestrator View
 *
 * Mengikuti arsitektur Anti-Monolith & Clean Views (rule.md).
 * Bertindak sebagai orchestrator tata letak modular dengan memanggil view partials ber-prefix '_'.
 * Styling modular terpusat pada assets/scss/pages/_admin_user_mgt.scss (via css/app.css).
 *
 * @var array  $users                  Daftar user internal yang memiliki role
 * @var array  $roles                  Daftar role sistem
 * @var array  $allUsers               Daftar seluruh karyawan (direktori DB)
 * @var int    $total                  Total user dengan role
 * @var int    $allUserTotal           Total karyawan di direktori DB
 * @var string $paginationHTML         Pagination CI4 untuk user dengan role
 * @var string $allUsersPaginationHTML Pagination CI4 untuk master direktori
 */
?>
<?php include(APPPATH . 'Views/components/common/_warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_error_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_invalid_confirm.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HC Helpdesk</title>
    <!-- Modern Bulma & Theme Compiled CSS (Single Source of Truth) -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php 
        $active = 'settings'; 
        $subActive = 'user_mgt';
        include(APPPATH . 'Views/admin/navbar.php'); 
    ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'System Settings',
            'breadcrumbActive' => 'Users',
            'pageTitle'        => 'User Management',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- 1. Nav Switcher Tabs: Assigned Roles vs All Users Directory -->
            <?= view('components/admin/user_mgt/_nav_tabs', [
                'total'        => $total ?? count($users ?? []),
                'allUserTotal' => $allUserTotal ?? 0
            ]) ?>

            <!-- 2. TAB 1: Assigned Role Users Content Card -->
            <div id="tab-content-users-role" class="settings-content-card" style="display: block;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Internal Users & Authentication</h2>
                            <span class="headline-badge badge-rbac">Accounts</span>
                        </div>
                        <p class="headline-desc">Daftar akun staf & pengelola yang memiliki hak akses role pada sistem helpdesk.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="user-search-input" class="settings-search-input" placeholder="Filter users with role..." onkeyup="filterUserTable(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" onclick="openAddUserModal()">
                            <span style="font-size:16px;">+</span>
                            <span>Add New User</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic list container -->
                <div id="user-mgt-list-wrap">
                    <?= view('admin/user_mgt_list', [
                        'users'          => $users ?? [],
                        'roles'          => $roles ?? [],
                        'paginationHTML' => $paginationHTML ?? ''
                    ]) ?>
                </div>
            </div>

            <!-- 3. TAB 2: All Users Directory Content Card -->
            <div id="tab-content-all-users" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Master Direktori Seluruh Karyawan & User</h2>
                            <span class="headline-badge badge-catalog">All Employees</span>
                        </div>
                        <p class="headline-desc">Menampilkan seluruh data karyawan yang ada di database lengkap dengan NIP (Employee No), Nama Lengkap, Posisi, dan Position Level.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="all-user-search-input" class="settings-search-input" placeholder="Filter seluruh user (NIP / Nama / Posisi)..." onkeyup="filterAllUserTable(this.value)">
                        </div>
                    </div>
                </div>

                <!-- Async dynamic all users directory container -->
                <div id="all-user-directory-wrap">
                    <?= view('admin/user_all_directory_list', [
                        'allUsers'               => $allUsers ?? [],
                        'allUsersPaginationHTML' => $allUsersPaginationHTML ?? ''
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Modular Modals Component (Add User & Edit User) -->
    <?= view('components/admin/user_mgt/_user_modals', [
        'roles' => $roles ?? []
    ]) ?>

    <!-- Context Endpoints for Modular JS -->
    <script>
        window.USER_MGT_CONFIG = {
            searchCandidateUrl: '<?= base_url('admin/search_candidate_users') ?>'
        };
    </script>
    <!-- Dedicated Modular JS -->
    <script src="<?= base_url('assets/js/modules/admin-user-mgt.js') ?>?v=<?= time() ?>"></script>
</body>
</html>