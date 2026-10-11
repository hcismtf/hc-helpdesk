<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;
use App\Models\RequestTypeModel;
use App\Models\SlaModel;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\RolePermissionsModel;

class SystemSettingsController extends BaseController
{
    protected \App\Services\SystemSettingsService $settingsService;

    public function __construct()
    {
        $this->settingsService = new \App\Services\SystemSettingsService();
    }

    /**
     * Main system settings view
     */
    public function system_settings()
    {
        $counts = $this->settingsService->getCounts();

        $faqModel = new FaqModel();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll(10);

        $requestTypeModel = new RequestTypeModel();
        $requestTypes = $requestTypeModel->findAll();

        $slaModel = new SlaModel();
        $usedRequestTypeIds = array_column($slaModel->findAll(), 'request_type_id');

        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->orderBy('name', 'asc')->findAll();

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $total = $faqModel->countAllResults();
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        $editFaq = null;
        $editId = $this->request->getGet('edit_faq_id');
        if ($editId) {
            $editFaq = $faqModel->find($editId);
        }

        $rolesData = $this->settingsService->getEnrichedRoles(1, 10);
        $slasData = $this->settingsService->getEnrichedSlas(1, 10);

        $categoryModel = new \App\Models\CategoryModel();
        $categories = $categoryModel->getActiveCategories();
        $allSlas = $slaModel->orderBy('priority', 'asc')->findAll();

        $data = [
            'username' => session('username') ?? '[user name]',
            'role' => session('role') ?? 'Superadmin',
            'counts' => $counts,
            'rolesData' => $rolesData,
            'slasData' => $slasData,
            'faqs' => $faqs,
            'editFaq' => $editFaq,
            'permissions' => $permissions,
            'requestTypes' => $requestTypes,
            'usedRequestTypeIds' => $usedRequestTypeIds,
            'categories' => $categories,
            'allSlas' => $allSlas,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'active' => 'settings',
            'subActive' => $this->request->getGet('tab') ?? 'roles',
        ];

        return view('admin/system_settings', $data);
    }

    /**
     * Dedicated Roles & Permissions Management Page
     * Khusus untuk mengelola Roles tanpa tab navigasi ke SLA atau FAQ.
     */
    public function roles_category()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $rolesData    = $this->settingsService->getEnrichedRoles($page, $perPage);
        $permissionModel = new PermissionModel();
        $permissions     = $permissionModel->orderBy('name', 'asc')->findAll();

        $paginationHTML = $this->generatePaginationHTML(
            $page, 
            $rolesData['totalPages'], 
            base_url('admin/system_settings/roles'), 
            '&per_page=' . $perPage,
            $rolesData['total'],
            $perPage
        );

        return view('admin/roles_category', [
            'username'       => session('username') ?? '[user name]',
            'role'           => session('role') ?? 'Superadmin',
            'rolesData'      => $rolesData,
            'permissions'    => $permissions,
            'paginationHTML' => $paginationHTML,
            'page'           => $page,
            'perPage'        => $perPage,
            'totalPages'     => $rolesData['totalPages'],
            'active'         => 'settings',
            'subActive'      => 'roles_category',
        ]);
    }

    /**
     * Dedicated Standalone SLA Policies Management Page
     */
    public function sla_policies()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $slasData = $this->settingsService->getEnrichedSlas($page, $perPage);

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $slasData['totalPages'],
            base_url('admin/system_settings/sla'),
            '&per_page=' . $perPage,
            $slasData['total'],
            $perPage
        );

        return view('admin/sla_policies', [
            'username'       => session('username') ?? '[user name]',
            'role'           => session('role') ?? 'Superadmin',
            'slasData'       => $slasData,
            'paginationHTML' => $paginationHTML,
            'page'           => $page,
            'perPage'        => $perPage,
            'totalPages'     => $slasData['totalPages'],
            'active'         => 'settings',
            'subActive'      => 'sla_policies',
        ]);
    }

    /**
     * Dedicated Standalone Request Category / Types Page
     */
    public function request_types()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $data = $this->settingsService->getEnrichedRequestTypes($page, $perPage);

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $data['totalPages'],
            base_url('admin/system_settings/request_types'),
            '&per_page=' . $perPage,
            $data['total'],
            $perPage
        );

        $mainCatData = $this->settingsService->getEnrichedCategories(1, 5);

        $mainCategoryPaginationHTML = $this->generatePaginationHTML(
            1,
            $mainCatData['totalPages'],
            base_url('admin/system_settings/request_types'),
            '&tab=main_category&per_page=5',
            $mainCatData['total'],
            5
        );

        return view('admin/request_types_category', [
            'username'                   => session('username') ?? '[user name]',
            'role'                       => session('role') ?? 'Superadmin',
            'types'                      => $data['types'],
            'categories'                 => $data['categories'],
            'mainCategories'             => $mainCatData['categories'],
            'mainCategoryTotal'          => $mainCatData['total'],
            'mainCategoryTotalPages'     => $mainCatData['totalPages'],
            'mainCategoryPage'           => 1,
            'mainCategoryPerPage'        => 5,
            'mainCategoryPaginationHTML' => $mainCategoryPaginationHTML,
            'slas'                       => $data['slas'],
            'paginationHTML'             => $paginationHTML,
            'page'                       => $page,
            'perPage'                    => $perPage,
            'totalPages'                 => $data['totalPages'],
            'total'                      => $data['total'],
            'active'                     => 'settings',
            'subActive'                  => 'request_category',
        ]);
    }

    /**
     * Dedicated Standalone FAQ Management Page
     */
    public function faqs()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $faqModel = new FaqModel();
        $total = $faqModel->countAll();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll($perPage, ($page - 1) * $perPage);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $totalPages,
            base_url('admin/system_settings/faqs'),
            '&per_page=' . $perPage,
            $total,
            $perPage
        );

        return view('admin/faq_management', [
            'username'       => session('username') ?? '[user name]',
            'role'           => session('role') ?? 'Superadmin',
            'faqs'           => $faqs,
            'paginationHTML' => $paginationHTML,
            'page'           => $page,
            'perPage'        => $perPage,
            'totalPages'     => $totalPages,
            'active'         => 'settings',
            'subActive'      => 'faq_management',
        ]);
    }

    // =========================================================================
    // FAQ Management
    // =========================================================================

    public function add_faq()
    {
        $question = $this->request->getPost('question');
        $answer = $this->request->getPost('answer');
        $createdBy = session('username') ?? 'admin';

        $faqModel = new FaqModel();
        $faqModel->insert([
            'question' => $question,
            'answer' => $answer,
            'created_by' => $createdBy,
            'created_date' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function edit_faq()
    {
        $id = $this->request->getPost('id');
        $question = $this->request->getPost('question');
        $answer = $this->request->getPost('answer');
        $modifiedBy = session('username') ?? 'admin';
        $modifiedDate = date('Y-m-d H:i:s');

        $faqModel = new FaqModel();
        $faqModel->update($id, [
            'question' => $question,
            'answer' => $answer,
            'modified_by' => $modifiedBy,
            'modified_date' => $modifiedDate
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function get_faq_list()
    {
        $faqModel = new FaqModel();
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $total = $faqModel->countAll();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll($perPage, ($page - 1) * $perPage);
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $totalPages,
            base_url('admin/system_settings/faqs'),
            '&per_page=' . $perPage,
            $total,
            $perPage
        );

        return view('admin/faq_list', [
            'faqs' => $faqs,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'paginationHTML' => $paginationHTML
        ]);
    }

    public function delete_faq()
    {
        $id = $this->request->getPost('id');
        $faqModel = new FaqModel();
        $faqModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

    // =========================================================================
    // User Role Management
    // =========================================================================

    public function add_user_role()
    {
        $name = $this->request->getPost('name');
        $permissions = $this->request->getPost('permissions');
        $createdBy = session('username') ?? 'admin';
        $createdDate = date('Y-m-d H:i:s');

        if (!$name || empty(trim($name))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role Name wajib diisi!'
            ]);
        }

        if (!is_array($permissions) || count($permissions) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu Permission wajib dipilih!'
            ]);
        }

        $roleModel = new RoleModel();

        try {
            $roleId = $roleModel->insert([
                'name' => $name,
                'created_by' => $createdBy,
                'created_date' => $createdDate
            ]);

            if ($roleId && is_array($permissions)) {
                $roleModel->syncPermissions($roleId, $permissions);
            }

            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambah User Role: ' . $e->getMessage()
            ]);
        }
    }

    public function edit_user_role()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $permissions = $this->request->getPost('permissions');

        $roleModel = new RoleModel();

        $roleModel->update($id, [
            'name' => $name,
            'modified_by' => session('username'),
            'modified_date' => date('Y-m-d H:i:s')
        ]);

        if (is_array($permissions)) {
            $roleModel->syncPermissions($id, $permissions);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function delete_user_role()
    {
        $id = $this->request->getPost('id');
        $roleModel = new RoleModel();
        $rolePermissionsModel = new RolePermissionModel();

        $rolePermissionsModel->where('role_id', $id)->delete();
        $deleted = $roleModel->delete($id);

        return $this->response->setJSON(['success' => (bool) $deleted]);
    }

    public function get_user_role_list()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $data = $this->settingsService->getEnrichedRoles($page, $perPage);
        $paginationHTML = $this->generatePaginationHTML(
            $page, 
            $data['totalPages'], 
            base_url('admin/system_settings/roles'), 
            '&per_page=' . $perPage,
            $data['total'],
            $perPage
        );

        return view('admin/user_role_list', [
            'roles' => $data['roles'],
            'page' => $page,
            'totalPages' => $data['totalPages'],
            'perPage' => $perPage,
            'paginationHTML' => $paginationHTML
        ]);
    }

    // =========================================================================
    // Request Type Management
    // =========================================================================

    public function get_request_type_list()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $data = $this->settingsService->getEnrichedRequestTypes($page, $perPage);
        $paginationHTML = $this->generatePaginationHTML(
            $page, 
            $data['totalPages'], 
            base_url('admin/system_settings/request_types'), 
            '&per_page=' . $perPage,
            $data['total'],
            $perPage
        );

        return view('admin/request_type', [
            'types' => $data['types'],
            'categories' => $data['categories'],
            'slas' => $data['slas'],
            'page' => $page,
            'totalPages' => $data['totalPages'],
            'perPage' => $perPage,
            'paginationHTML' => $paginationHTML
        ]);
    }

    public function get_request_type_detail($id)
    {
        $model = new RequestTypeModel();
        $type = $model->find($id);
        if ($type) {
            return $this->response->setJSON(['success' => true, 'data' => $type]);
        }
        return $this->response->setJSON(['success' => false]);
    }

    public function add_request_type()
    {
        $name = $this->request->getPost('name');
        $categoryId = $this->request->getPost('category_id') ?: null;
        $slaId = $this->request->getPost('sla_id') ?: null;
        $badgeTag = $this->request->getPost('badge_tag');
        $isPopular = $this->request->getPost('is_popular') ? 1 : 0;
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status') ?? 'Active';
        $createdBy = session('username') ?? 'admin';

        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->insert([
            'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'sla_id' => $slaId,
            'badge_tag' => $badgeTag,
            'is_popular' => $isPopular,
            'status' => $status,
            'created_by' => $createdBy,
            'created_date' => date('Y-m-d H:i:s'),
            'modified_by' => $createdBy,
            'modified_date' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function edit_request_type()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $categoryId = $this->request->getPost('category_id') ?: null;
        $slaId = $this->request->getPost('sla_id') ?: null;
        $badgeTag = $this->request->getPost('badge_tag');
        $isPopular = $this->request->getPost('is_popular') ? 1 : 0;
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');
        $modifiedBy = session('username') ?? 'admin';
        $modifiedDate = date('Y-m-d H:i:s');

        $model = new RequestTypeModel();
        $model->update($id, [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'sla_id' => $slaId,
            'badge_tag' => $badgeTag,
            'is_popular' => $isPopular,
            'status' => $status,
            'modified_by' => $modifiedBy,
            'modified_date' => $modifiedDate
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function update_request_type()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');

        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->update($id, [
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]);

        return redirect()->to(base_url('admin/system_settings?tab=request-type'));
    }

    public function delete_request_type()
    {
        $id = $this->request->getPost('id');
        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

    // =========================================================================
    // Main Category (Topik HC) Management
    // =========================================================================

    public function get_category_list()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $data = $this->settingsService->getEnrichedCategories($page, $perPage);
        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $data['totalPages'],
            base_url('admin/system_settings/request_types'),
            '&tab=main_category&per_page=' . $perPage,
            $data['total'],
            $perPage
        );

        return view('admin/request_main_category', [
            'categories'     => $data['categories'],
            'page'           => $page,
            'totalPages'     => $data['totalPages'],
            'perPage'        => $perPage,
            'paginationHTML' => $paginationHTML
        ]);
    }

    public function get_category_detail($id)
    {
        $categoryModel = new \App\Models\CategoryModel();
        $cat = $categoryModel->find($id);
        if ($cat) {
            return $this->response->setJSON(['success' => true, 'data' => is_array($cat) ? $cat : $cat->toArray()]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Category not found']);
    }

    public function add_category()
    {
        $name        = trim((string) $this->request->getPost('name'));
        $code        = trim((string) $this->request->getPost('code'));
        $description = trim((string) $this->request->getPost('description'));
        $icon        = trim((string) $this->request->getPost('icon')) ?: 'fa-solid fa-folder';
        $color       = trim((string) $this->request->getPost('color')) ?: '#2563EB';
        $sortOrder   = (int) ($this->request->getPost('sort_order') ?? 0);
        $isActive    = (int) ($this->request->getPost('is_active') ?? 1);
        $createdBy   = session('username') ?? 'admin';

        if (empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nama kategori wajib diisi!']);
        }

        $categoryModel = new \App\Models\CategoryModel();
        $id = \Ramsey\Uuid\Uuid::uuid4()->toString();

        $categoryModel->insert([
            'id'            => $id,
            'code'          => strtoupper($code ?: substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 10)),
            'name'          => $name,
            'description'   => $description,
            'icon'          => $icon,
            'color'         => $color,
            'sort_order'    => $sortOrder,
            'is_active'     => $isActive,
            'created_by'    => $createdBy,
            'created_date'  => date('Y-m-d H:i:s'),
            'modified_by'   => $createdBy,
            'modified_date' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function edit_category()
    {
        $id          = $this->request->getPost('id');
        $name        = trim((string) $this->request->getPost('name'));
        $code        = trim((string) $this->request->getPost('code'));
        $description = trim((string) $this->request->getPost('description'));
        $icon        = trim((string) $this->request->getPost('icon')) ?: 'fa-solid fa-folder';
        $color       = trim((string) $this->request->getPost('color')) ?: '#2563EB';
        $sortOrder   = (int) ($this->request->getPost('sort_order') ?? 0);
        $isActive    = (int) ($this->request->getPost('is_active') ?? 1);
        $modifiedBy  = session('username') ?? 'admin';

        if (empty($id) || empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $categoryModel = new \App\Models\CategoryModel();
        $categoryModel->update($id, [
            'code'          => strtoupper($code),
            'name'          => $name,
            'description'   => $description,
            'icon'          => $icon,
            'color'         => $color,
            'sort_order'    => $sortOrder,
            'is_active'     => $isActive,
            'modified_by'   => $modifiedBy,
            'modified_date' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete_category()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID kategori tidak valid']);
        }

        // Cek apakah ada Sub Category yang sedang menggunakan category ini
        $db = \Config\Database::connect();
        $usedCount = $db->table('request_sub_category')->where('category_id', $id)->countAllResults();
        if ($usedCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'invalid' => true,
                'message' => "Kategori ini masih digunakan oleh {$usedCount} Request Type (Sub Kategori). Silakan pindahkan atau hapus sub-kategori terkait terlebih dahulu."
            ]);
        }

        $categoryModel = new \App\Models\CategoryModel();
        $categoryModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

    // =========================================================================

    // SLA Settings Management
    // =========================================================================

    public function add_sla()
    {
        $priority = $this->request->getPost('priority');
        $responseTime = $this->request->getPost('response_time');
        $resolutionTime = $this->request->getPost('resolution_time');
        $createdBy = session('username') ?? 'admin';
        $createdDate = date('Y-m-d H:i:s');

        $slaModel = new SlaModel();
        $slaModel->insert([
            'priority' => $priority,
            'response_time' => $responseTime,
            'resolution_time' => $resolutionTime,
            'created_by' => $createdBy,
            'created_date' => $createdDate
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function get_sla_list()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $data = $this->settingsService->getEnrichedSlas($page, $perPage);

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $data['totalPages'],
            base_url('admin/system_settings/sla'),
            '&per_page=' . $perPage,
            $data['total'],
            $perPage
        );

        return view('admin/sla_settings', [
            'slas' => $data['slas'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $data['totalPages'],
            'paginationHTML' => $paginationHTML
        ]);
    }

    public function edit_sla()
    {
        $id = $this->request->getPost('id');
        $priority = $this->request->getPost('priority');
        $responseTime = $this->request->getPost('response_time');
        $resolutionTime = $this->request->getPost('resolution_time');
        $modifiedBy = session('username') ?? 'admin';
        $modifiedDate = date('Y-m-d H:i:s');

        $slaModel = new SlaModel();
        $slaModel->update($id, [
            'priority' => $priority,
            'response_time' => $responseTime,
            'resolution_time' => $resolutionTime,
            'modified_by' => $modifiedBy,
            'modified_date' => $modifiedDate
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete_sla()
    {
        $id = $this->request->getPost('id');
        $slaModel = new SlaModel();
        $slaModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

    public function get_used_request_types()
    {
        $slaModel = new SlaModel();
        $usedRequestTypeIds = array_column($slaModel->findAll(), 'request_type_id');
        return $this->response->setJSON(['used' => $usedRequestTypeIds]);
    }

    // =========================================================================
    // Permission Management
    // =========================================================================

    public function add_permission()
    {
        $name = $this->request->getPost('name');
        $code = $this->request->getPost('code');
        $model = new PermissionModel();

        $data = [
            'name' => $name,
            'code' => $code,
            'created_by' => session('username'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $id = $model->insert($data);
        if ($id) {
            $permission = $model->find($id);
            return $this->response->setJSON(['success' => true, 'permission' => $permission]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    public function get_permission()
    {
        $id = $this->request->getGet('id');
        $model = new PermissionModel();
        $permission = $model->find($id);

        if ($permission) {
            return $this->response->setJSON(['success' => true, 'permission' => $permission]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    public function edit_permission()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $code = $this->request->getPost('code');
        $model = new PermissionModel();

        $data = [
            'name' => $name,
            'code' => $code,
            'modified_by' => session('username'),
            'modified_date' => date('Y-m-d H:i:s')
        ];

        $model->update($id, $data);
        $permission = $model->find($id);

        if ($permission) {
            return $this->response->setJSON(['success' => true, 'permission' => $permission]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    public function delete_permission()
    {
        $id = $this->request->getPost('id');
        $model = new PermissionModel();

        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }
}
