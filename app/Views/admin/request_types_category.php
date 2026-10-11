<?php
/**
 * Request Category & Types (Sub Category) Page Orchestrator View
 *
 * Mengikuti arsitektur Anti-Monolith & Clean Views (rule.md).
 * Bertindak sebagai orchestrator tata letak modular dengan memanggil view partials ber-prefix '_'.
 * Styling modular terpusat pada assets/scss/pages/_admin_request_types_category.scss (via css/app.css).
 *
 * @var array  $types                       Daftar request types (sub-kategori)
 * @var array  $categories                  Daftar topik kategori utama (dropdown options)
 * @var array  $mainCategories              Daftar topik kategori utama (table list)
 * @var array  $slas                        Daftar SLA
 * @var int    $total                       Total count sub-kategori
 * @var int    $mainCategoryTotal           Total count main-kategori
 * @var int    $page                        Halaman aktif sub-kategori
 * @var int    $perPage                     Per page sub-kategori
 * @var int    $totalPages                  Total halaman sub-kategori
 * @var string $paginationHTML              Markup pagination CI4 sub-kategori
 * @var int    $mainCategoryPage            Halaman aktif main-kategori
 * @var int    $mainCategoryPerPage         Per page main-kategori
 * @var int    $mainCategoryTotalPages      Total halaman main-kategori
 * @var string $mainCategoryPaginationHTML  Markup pagination CI4 main-kategori
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
    <title>Request Category & Types - HC Helpdesk</title>
    <!-- Modern Bulma & Theme Compiled CSS (Single Source of Truth) -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php 
        $active = 'settings'; 
        $subActive = 'request_category';
        include('navbar.php'); 
    ?>

    <div class="main-content">
        <!-- Reusable Top Header Component -->
        <?= view('components/admin/admin_header', [
            'breadcrumbRoot'   => 'System Settings',
            'breadcrumbActive' => 'Request Category',
            'pageTitle'        => 'Request Category & Types',
            'showCreateTicket' => true,
            'showNotif'        => true,
        ]) ?>

        <div class="settings-page-wrapper">
            <!-- 1. Nav Switcher Tabs: Sub Category vs Main Category -->
            <?= view('components/admin/request_category/_nav_tabs', [
                'total'             => $total ?? count($types ?? []),
                'mainCategoryTotal' => $mainCategoryTotal ?? count($mainCategories ?? [])
            ]) ?>

            <!-- 2. TAB 1: Sub Category (Request Type) Catalog -->
            <div id="tab-content-sub_category" class="settings-content-card" style="display: block;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Request Type Catalog (Sub Category)</h2>
                            <span class="headline-badge badge-catalog">Sub Category</span>
                        </div>
                        <p class="headline-desc">Kelola klasifikasi sub-kategori pengajuan tiket, relasi kategori utama topik HC, dan kebijakan SLA.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="reqtype-search-input" class="settings-search-input" placeholder="Filter request types..." onkeyup="filterRequestTypeTable(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" id="btn-add-request-type">
                            <span style="font-size:16px;">+</span>
                            <span>Add Request Type</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic list container -->
                <div id="request-type-list">
                    <?= view('admin/request_type', [
                        'types'          => $types ?? [],
                        'categories'     => $categories ?? [],
                        'slas'           => $slas ?? [],
                        'page'           => $page ?? 1,
                        'perPage'        => $perPage ?? 5,
                        'totalPages'     => $totalPages ?? 1,
                        'paginationHTML' => $paginationHTML ?? ''
                    ]) ?>
                </div>
            </div>

            <!-- 3. TAB 2: Main Category (Topik HC) Management -->
            <div id="tab-content-main_category" class="settings-content-card" style="display: none;">
                <div class="settings-section-header">
                    <div class="section-headline-group">
                        <div class="headline-title-row">
                            <h2 class="headline-title">Main Category (Topik HC)</h2>
                            <span class="headline-badge badge-role">Main Topic</span>
                        </div>
                        <p class="headline-desc">Kelola pilar kategori utama topik layanan HC (Presensi, Payroll, Asuransi, Akun & Fasilitas, dll.) yang menjadi induk dari Sub Kategori.</p>
                    </div>
                    <div class="section-actions-row">
                        <div class="search-input-wrapper">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="category-search-input" class="settings-search-input" placeholder="Filter kategori utama..." onkeyup="filterCategoryTable(this.value)">
                        </div>
                        <button type="button" class="btn-primary-add" id="btn-add-category">
                            <span style="font-size:16px;">+</span>
                            <span>Add Main Category</span>
                        </button>
                    </div>
                </div>

                <!-- Async dynamic category list container -->
                <div id="main-category-list">
                    <?= view('admin/request_main_category', [
                        'categories'     => $mainCategories ?? [],
                        'page'           => $mainCategoryPage ?? 1,
                        'perPage'        => $mainCategoryPerPage ?? 5,
                        'totalPages'     => $mainCategoryTotalPages ?? 1,
                        'paginationHTML' => $mainCategoryPaginationHTML ?? ''
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Modular Modals Component (Add, Edit, Delete for Sub & Main Categories) -->
    <?= view('components/admin/request_category/_category_modals', [
        'categories' => $categories ?? [],
        'slas'       => $slas ?? []
    ]) ?>

    <!-- Context Endpoints for Modular JS -->
    <script>
        window.REQ_CATEGORY_ENDPOINTS = {
            addTypeUrl: '<?= base_url('admin/add_request_type') ?>',
            editTypeUrl: '<?= base_url('admin/edit_request_type') ?>',
            deleteTypeUrl: '<?= base_url('admin/delete_request_type') ?>',
            listTypeUrl: '<?= base_url('admin/get_request_type_list') ?>',
            addCatUrl: '<?= base_url('admin/add_category') ?>',
            editCatUrl: '<?= base_url('admin/edit_category') ?>',
            deleteCatUrl: '<?= base_url('admin/delete_category') ?>',
            listCatUrl: '<?= base_url('admin/get_category_list') ?>'
        };
    </script>
    <!-- Dedicated Modular JS -->
    <script src="<?= base_url('assets/js/modules/admin-request-types-category.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
