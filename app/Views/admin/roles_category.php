<?php
/**
 * Roles & Permissions Management Page Orchestrator View
 *
 * Mengikuti arsitektur Anti-Monolith & Clean Views (rule.md).
 * Bertindak sebagai orchestrator tata letak modular dengan memanggil view partials ber-prefix '_'.
 * Styling modular terpusat pada assets/scss/pages/_admin_roles_category.scss (via css/app.css).
 *
 * @var array  $rolesData      Daftar roles yang telah diperkaya (permissions, member count, avatar)
 * @var array  $permissions    Daftar master permissions untuk opsi form
 * @var string $paginationHTML Markup pagination CI4
 */
?>
<?php include(APPPATH . 'Views/components/common/_success_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_warning_confirm.php'); ?>
<?php include(APPPATH . 'Views/components/common/_invalid_confirm.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions - HC Helpdesk</title>
    <!-- Modern Bulma & Theme Compiled CSS (Single Source of Truth) -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php 
        $active = 'settings'; 
        $subActive = 'roles_category';
        include('navbar.php'); 
    ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'System Settings',
            'breadcrumbActive' => 'Roles & Permissions',
            'pageTitle'        => 'Roles & Permissions',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- Dedicated Roles & Permissions Management Card -->
            <div id="tab-content-roles" class="settings-content-card" style="display: block;">
                <!-- 1. Section Headline, Search Bar & Add Button Component -->
                <?= view('components/admin/roles/_section_header') ?>

                <!-- 2. Async Dynamic List Container -->
                <div id="user-role-list">
                    <?= view('admin/user_role_list', [
                        'roles'          => $rolesData['roles'] ?? [], 
                        'paginationHTML' => $paginationHTML ?? ''
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Modular Modals Component (Add, Edit, Delete Roles) -->
    <?= view('components/admin/roles/_role_modals', [
        'permissions' => $permissions ?? []
    ]) ?>

    <!-- Context Endpoints & Master Data for JS Module -->
    <script>
        window.ROLE_ENDPOINTS = {
            addUrl: '<?= base_url('admin/add_user_role') ?>',
            editUrl: '<?= base_url('admin/edit_user_role') ?>',
            deleteUrl: '<?= base_url('admin/delete_user_role') ?>',
            listUrl: '<?= base_url('admin/get_user_role_list') ?>'
        };
        window.PERMISSIONS_MASTER = <?= json_encode($permissions ?? []) ?>;
    </script>
    <!-- Dedicated Modular JS -->
    <script src="<?= base_url('assets/js/modules/admin-roles-category.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
