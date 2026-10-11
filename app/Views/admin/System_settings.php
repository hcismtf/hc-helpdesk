<?php 
include(APPPATH . 'Views/components/common/_success_confirm.php');
include(APPPATH . 'Views/components/common/_warning_confirm.php');
include(APPPATH . 'Views/components/common/_invalid_confirm.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings - HC Helpdesk</title>
    <!-- Modern Bulma & Theme Compiled CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
</head>
<body>
    <?php $active = 'settings'; include('navbar.php'); ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'Helpdesk Admin',
            'breadcrumbActive' => 'System Settings',
            'pageTitle'        => 'System Settings',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- Top Pill-Shaped Navigation Bar Component -->
            <?= view('components/admin/system_settings/_nav_bar', [
                'counts' => $counts ?? []
            ]) ?>

            <!-- TAB 1: ROLES & PERMISSIONS -->
            <div id="tab-content-roles" class="settings-content-card" style="display: block;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">User Roles & Permissions</h2>
                            <span class="headline-badge badge-rbac">RBAC</span>
                        </div>
                        <p class="headline-desc">Define access levels, module permissions, and assigned personnel across your workspace.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="role-search-input" class="settings-search-input" placeholder="Filter roles..." onkeyup="filterRolesTable(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" id="btn-add-user-role">
                            <span style="font-size:16px;">+</span>
                            <span>Add New Role</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic list container -->
                <div id="user-role-list">
                    <?= view('admin/user_role_list', ['roles' => $rolesData['roles'], 'paginationHTML' => $paginationHTML ?? '']) ?>
                </div>
            </div>

            <!-- TAB 2: SLA POLICIES -->
            <div id="tab-content-sla" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">SLA Response & Resolution Policies</h2>
                            <span class="headline-badge badge-engine">Global Engine</span>
                        </div>
                        <p class="headline-desc">Configure target response times, resolution deadlines, and automatic escalation pathways by priority tier.</p>
                    </div>
                    <div class="section-actions-row">
                        <button type="button" class="btn-primary-add" id="btn-add-sla">
                            <span style="font-size:16px;">+</span>
                            <span>New SLA Rule</span>
                        </button>
                    </div>
                </div>

                <div id="sla-list">
                    <?= view('admin/sla_settings', ['slas' => $slasData['slas'], 'totalPages' => $slasData['totalPages'], 'page' => 1, 'perPage' => 10]) ?>
                </div>
            </div>

            <!-- TAB 3: REQUEST TYPE -->
            <div id="tab-content-request_type" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Request Type Catalog</h2>
                            <span class="headline-badge badge-catalog">Catalog</span>
                        </div>
                        <p class="headline-desc">Manage categories and submission classifications available for employee ticket filing.</p>
                    </div>
                    <div class="section-actions-row">
                        <button type="button" class="btn-primary-add" id="btn-add-request-type">
                            <span style="font-size:16px;">+</span>
                            <span>Add Request Type</span>
                        </button>
                    </div>
                </div>

                <div id="request-type-list">
                    <?= view('admin/request_type', ['types' => $requestTypes]) ?>
                </div>
            </div>

            <!-- TAB 4: FAQ MANAGEMENT -->
            <div id="tab-content-faq" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Knowledge Base & FAQ Articles</h2>
                            <span class="headline-badge badge-rbac">Articles</span>
                        </div>
                        <p class="headline-desc">Publish self-service guidance and troubleshooting answers for employees.</p>
                    </div>
                    <div class="section-actions-row">
                        <button type="button" class="btn-primary-add" id="btn-add-faq">
                            <span style="font-size:16px;">+</span>
                            <span>Add New FAQ</span>
                        </button>
                    </div>
                </div>

                <div id="faq-list">
                    <?= view('admin/faq_list', ['faqs' => $faqs, 'page' => $page, 'totalPages' => $totalPages, 'perPage' => $perPage, 'paginationHTML' => $paginationHTML ?? '']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Component (Role, SLA, Request Type, FAQ) -->
    <?= view('components/admin/system_settings/_settings_modals', [
        'permissions' => $permissions ?? [],
        'categories'  => $categories ?? [],
        'slas'        => $slas ?? [],
    ]) ?>

    <!-- Config Variables for JS Module -->
    <script>
        window.SYSTEM_SETTINGS_ACTIVE_TAB = '<?= esc($subActive ?? 'roles') ?>';
        window.SYSTEM_SETTINGS_PERMISSIONS = <?= json_encode($permissions ?? []) ?>;
        window.SYSTEM_SETTINGS_ENDPOINTS = {
            addRole: '<?= base_url('admin/add_user_role') ?>',
            editRole: '<?= base_url('admin/edit_user_role') ?>',
            deleteRole: '<?= base_url('admin/delete_user_role') ?>',
            getRoleList: '<?= base_url('admin/get_user_role_list') ?>',
            addSla: '<?= base_url('admin/add_sla') ?>',
            editSla: '<?= base_url('admin/edit_sla') ?>',
            deleteSla: '<?= base_url('admin/delete_sla') ?>',
            getSlaList: '<?= base_url('admin/get_sla_list') ?>',
            addRequestType: '<?= base_url('admin/add_request_type') ?>',
            editRequestType: '<?= base_url('admin/edit_request_type') ?>',
            deleteRequestType: '<?= base_url('admin/delete_request_type') ?>',
            getRequestTypeList: '<?= base_url('admin/get_request_type_list') ?>',
            addFaq: '<?= base_url('admin/add_faq') ?>',
            editFaq: '<?= base_url('admin/edit_faq') ?>',
            deleteFaq: '<?= base_url('admin/delete_faq') ?>',
            getFaqList: '<?= base_url('admin/get_faq_list') ?>'
        };
    </script>
    <script src="<?= base_url('assets/js/modules/admin-system-settings.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= base_url('assets/js/global_success.js') ?>"></script>
    <script src="<?= base_url('assets/js/global_warning.js') ?>"></script>
</body>
</html>