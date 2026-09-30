<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;
use App\Models\RequestTypeModel;
use App\Models\SlaModel;
use App\Models\PermissionsModel;
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

        $permissionsModel = new PermissionsModel();
        $permissions = $permissionsModel->orderBy('name', 'asc')->findAll();

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
        $slasData  = $this->settingsService->getEnrichedSlas(1, 10);

        $data = [
            'username'           => session('username') ?? '[user name]',
            'role'               => session('role') ?? 'Superadmin',
            'counts'             => $counts,
            'rolesData'          => $rolesData,
            'slasData'           => $slasData,
            'faqs'               => $faqs,
            'editFaq'            => $editFaq,
            'permissions'        => $permissions,
            'requestTypes'       => $requestTypes,
            'usedRequestTypeIds' => $usedRequestTypeIds,
            'page'               => $page,
            'perPage'            => $perPage,
            'totalPages'         => $totalPages
        ];

        return view('admin/System_settings', $data);
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
            'question'     => $question,
            'answer'       => $answer,
            'created_by'   => $createdBy,
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
            'question'      => $question,
            'answer'        => $answer,
            'modified_by'   => $modifiedBy,
            'modified_date' => $modifiedDate
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function get_faq_list()
    {
        $faqModel = new FaqModel();
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $total = $faqModel->countAll();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll($perPage, ($page - 1) * $perPage);
        $totalPages = $perPage > 0 ? ceil($total / $perPage) : 1;

        $paginationHTML = $this->generatePaginationHTML($page, $totalPages, base_url('admin/system_settings'), '&per_page=' . $perPage);

        return view('admin/faq_list', [
            'faqs'           => $faqs,
            'page'           => $page,
            'totalPages'     => $totalPages,
            'perPage'        => $perPage,
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
                'name'         => $name,
                'created_by'   => $createdBy,
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
            'name'          => $name,
            'modified_by'   => session('username'),
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
        $rolePermissionsModel = new RolePermissionsModel();

        $rolePermissionsModel->where('role_id', $id)->delete();
        $deleted = $roleModel->delete($id);

        return $this->response->setJSON(['success' => (bool)$deleted]);
    }

    public function get_user_role_list()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $data = $this->settingsService->getEnrichedRoles($page, $perPage);
        $paginationHTML = $this->generatePaginationHTML($page, $data['totalPages'], base_url('admin/system_settings'), '&per_page=' . $perPage);

        return view('admin/user_role_list', [
            'roles'          => $data['roles'],
            'page'           => $page,
            'totalPages'     => $data['totalPages'],
            'perPage'        => $perPage,
            'paginationHTML' => $paginationHTML
        ]);
    }

    // =========================================================================
    // Request Type Management
    // =========================================================================

    public function get_request_type_list()
    {
        $requestTypeModel = new RequestTypeModel();
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $total = $requestTypeModel->countAll();
        $types = $requestTypeModel->orderBy('id', 'desc')->findAll($perPage, ($page - 1) * $perPage);
        $totalPages = $perPage > 0 ? ceil($total / $perPage) : 1;

        $paginationHTML = $this->generatePaginationHTML($page, $totalPages, base_url('admin/system_settings'), '&per_page=' . $perPage);

        return view('admin/request_type', [
            'types'          => $types,
            'page'           => $page,
            'totalPages'     => $totalPages,
            'perPage'        => $perPage,
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
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');
        $createdBy = session('username') ?? 'admin';

        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->insert([
            'name'          => $name,
            'description'   => $description,
            'status'        => $status,
            'created_by'    => $createdBy,
            'created_date'  => date('Y-m-d H:i:s'),
            'modified_by'   => $createdBy,
            'modified_date' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function edit_request_type()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');
        $modifiedBy = session('username') ?? 'admin';
        $modifiedDate = date('Y-m-d H:i:s');

        $model = new RequestTypeModel();
        $model->update($id, [
            'name'          => $name,
            'description'   => $description,
            'status'        => $status,
            'modified_by'   => $modifiedBy,
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
            'name'        => $name,
            'description' => $description,
            'status'      => $status
        ]);

        return redirect()->to(base_url('admin/System_settings?tab=request-type'));
    }

    public function delete_request_type()
    {
        $id = $this->request->getPost('id');
        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->delete($id);

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
            'priority'        => $priority,
            'response_time'   => $responseTime,
            'resolution_time' => $resolutionTime,
            'created_by'      => $createdBy,
            'created_date'    => $createdDate
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function get_sla_list()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $data = $this->settingsService->getEnrichedSlas($page, $perPage);

        return view('admin/sla_settings', [
            'slas'       => $data['slas'],
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $data['totalPages']
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
            'priority'        => $priority,
            'response_time'   => $responseTime,
            'resolution_time' => $resolutionTime,
            'modified_by'     => $modifiedBy,
            'modified_date'   => $modifiedDate
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
        $model = new PermissionsModel();

        $data = [
            'name'         => $name,
            'code'         => $code,
            'created_by'   => session('username'),
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
        $model = new PermissionsModel();
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
        $model = new PermissionsModel();

        $data = [
            'name'          => $name,
            'code'          => $code,
            'modified_by'   => session('username'),
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
        $model = new PermissionsModel();

        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }
}
