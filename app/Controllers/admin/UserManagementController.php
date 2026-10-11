<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Services\AuthService;

class UserManagementController extends BaseController
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Display user management list (Hanya menampilkan user yang terdaftar memiliki role)
     */
    public function user_mgt()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $roleModel = new RoleModel();
        $roles = $roleModel->orderBy('name', 'asc')->findAll();

        $userModel = new UserModel();
        
        // Count total non-deleted users who have a role assigned
        $total = $userModel->groupStart()
            ->where('is_deleted !=', 1)
            ->orWhere('is_deleted IS NULL')
            ->groupEnd()
            ->where('role_id IS NOT NULL')
            ->where('role_id !=', '')
            ->countAllResults();
            
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        $db = \Config\Database::connect();
        $users = $db->table('users')
            ->select('users.*, role.name as role_name')
            ->join('role', 'role.id = users.role_id', 'inner')
            ->groupStart()
            ->where('users.is_deleted !=', 1)
            ->orWhere('users.is_deleted IS NULL')
            ->groupEnd()
            ->where('users.role_id IS NOT NULL')
            ->where('users.role_id !=', '')
            ->orderBy('users.created_date', 'desc')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->orderBy('name', 'asc')->findAll();

        $paginationHTML = $this->generatePaginationHTML(
            $page,
            $totalPages,
            base_url('admin/system_settings/users'),
            '&per_page=' . $perPage,
            $total,
            $perPage
        );

        // --- All Users in DB (All Employee Directory) ---
        $allUserPage = (int) ($this->request->getGet('all_page') ?? 1);
        $allUserPerPage = 5;
        
        $allUserTotal = $userModel->groupStart()
            ->where('is_deleted !=', 1)
            ->orWhere('is_deleted IS NULL')
            ->groupEnd()
            ->countAllResults();
        
        $allUserTotalPages = $allUserPerPage > 0 ? (int) ceil($allUserTotal / $allUserPerPage) : 1;

        $allUsers = $db->table('users')
            ->select('users.*, role.name as role_name')
            ->join('role', 'role.id = users.role_id', 'left')
            ->groupStart()
            ->where('users.is_deleted !=', 1)
            ->orWhere('users.is_deleted IS NULL')
            ->groupEnd()
            ->orderBy('users.employee_no', 'asc')
            ->limit($allUserPerPage, ($allUserPage - 1) * $allUserPerPage)
            ->get()
            ->getResultArray();

        $allUsersPaginationHTML = $this->generatePaginationHTML(
            $allUserPage,
            $allUserTotalPages,
            base_url('admin/system_settings/users'),
            '&tab=all_users&per_page=5',
            $allUserTotal,
            $allUserPerPage
        );

        return view('admin/user_mgt', [
            'active'                 => 'settings',
            'subActive'              => 'user_mgt',
            'roles'                  => $roles,
            'users'                  => $users,
            'permissions'            => $permissions,
            'page'                   => $page,
            'perPage'                => $perPage,
            'totalPages'             => $totalPages,
            'total'                  => $total,
            'paginationHTML'         => $paginationHTML,
            'allUsers'               => $allUsers,
            'allUserTotal'           => $allUserTotal,
            'allUserPage'            => $allUserPage,
            'allUserPerPage'         => $allUserPerPage,
            'allUserTotalPages'      => $allUserTotalPages,
            'allUsersPaginationHTML' => $allUsersPaginationHTML,
        ]);
    }

    /**
     * Search employee/user candidates for NIP - Nama searchable dropdown
     */
    public function search_candidate_users()
    {
        $keyword = trim($this->request->getGet('q') ?? '');
        $db = \Config\Database::connect();

        $builder = $db->table('users')
            ->select('id, employee_no, name, email, role_id, status, position')
            ->groupStart()
                ->where('is_deleted !=', 1)
                ->orWhere('is_deleted IS NULL')
            ->groupEnd();

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('employee_no', $keyword)
                ->orLike('name', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd();
        }

        $candidates = $builder->orderBy('employee_no', 'ASC')
            ->limit(30)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $candidates
        ]);
    }

    /**
     * AJAX reload for All Users Directory tab
     */
    public function get_all_users_list()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $userModel = new UserModel();
        $db = \Config\Database::connect();

        $allUserTotal = $userModel->groupStart()
            ->where('is_deleted !=', 1)
            ->orWhere('is_deleted IS NULL')
            ->groupEnd()
            ->countAllResults();

        $allUserTotalPages = $perPage > 0 ? (int) ceil($allUserTotal / $perPage) : 1;

        $allUsers = $db->table('users')
            ->select('users.*, role.name as role_name')
            ->join('role', 'role.id = users.role_id', 'left')
            ->groupStart()
            ->where('users.is_deleted !=', 1)
            ->orWhere('users.is_deleted IS NULL')
            ->groupEnd()
            ->orderBy('users.employee_no', 'asc')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $allUsersPaginationHTML = $this->generatePaginationHTML(
            $page,
            $allUserTotalPages,
            base_url('admin/system_settings/users'),
            '&tab=all_users&per_page=' . $perPage,
            $allUserTotal,
            $perPage
        );

        return view('admin/user_all_directory_list', [
            'allUsers'               => $allUsers,
            'allUserTotal'           => $allUserTotal,
            'allUserPage'            => $page,
            'allUserPerPage'         => $perPage,
            'allUserTotalPages'      => $allUserTotalPages,
            'allUsersPaginationHTML' => $allUsersPaginationHTML,
        ]);
    }

    /**
     * Add or assign role to user
     */
    public function add_user()
    {
        $userModel = new UserModel();

        $userId   = trim($this->request->getPost('user_id') ?? '');
        $roleId   = trim($this->request->getPost('role') ?? '');
        $status   = trim($this->request->getPost('status') ?? 'Active');
        $operator = session('username') ?? 'system';

        if (empty($roleId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role wajib dipilih!']);
        }

        // Jika user dipilih dari database (NIP - Nama)
        if (!empty($userId)) {
            $user = $userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan!']);
            }

            $userModel->update($userId, [
                'role_id'       => $roleId,
                'status'        => $status,
                'is_deleted'    => 0,
                'modified_by'   => $operator,
                'modified_date' => date('Y-m-d H:i:s'),
            ]);

            $updatedUser = $userModel->getUserWithRole($userId);
            return $this->response->setJSON(['success' => true, 'user' => $updatedUser]);
        }

        // Fallback jika input manual baru
        $email    = trim($this->request->getPost('email') ?? '');
        $name     = trim($this->request->getPost('name') ?? '');
        $nip      = trim($this->request->getPost('employee_no') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan pilih User (NIP - Nama)!']);
        }

        $existingUser = null;
        if (!empty($nip)) {
            $existingUser = $userModel->where('employee_no', $nip)->first();
        }
        if (!$existingUser && !empty($email)) {
            $existingUser = $userModel->where('email', $email)->first();
        }

        if ($existingUser) {
            $updateData = [
                'role_id'       => $roleId,
                'status'        => $status,
                'is_deleted'    => 0,
                'modified_by'   => $operator,
                'modified_date' => date('Y-m-d H:i:s'),
            ];
            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            $userModel->update($existingUser['id'], $updateData);
            $user = $userModel->getUserWithRole($existingUser['id']);
            return $this->response->setJSON(['success' => true, 'user' => $user]);
        }

        $newUserId = $this->authService->generateUUIDv4();
        $userData = [
            'id'           => $newUserId,
            'name'         => $name,
            'employee_no'  => $nip ?: null,
            'email'        => $email ?: null,
            'password'     => !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null,
            'status'       => $status,
            'role_id'      => $roleId,
            'is_deleted'   => 0,
            'created_by'   => $operator,
            'created_date' => date('Y-m-d H:i:s'),
        ];

        try {
            $userModel->insert($userData);
            $user = $userModel->getUserWithRole($newUserId);
            return $this->response->setJSON(['success' => true, 'user' => $user]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Soft delete a user
     */
    public function delete_user()
    {
        $id = $this->request->getPost('id');
        $userModel = new UserModel();

        $userModel->update($id, [
            'is_deleted' => 1,
            'modified_by' => session('username') ?? 'system',
            'modified_date' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Edit existing user
     */
    public function edit_user()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $roleId = $this->request->getPost('role');
        $status = $this->request->getPost('status');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();

        $updateData = [
            'name' => $name,
            'email' => $email,
            'status' => $status,
            'role_id' => $roleId,
            'modified_date' => date('Y-m-d H:i:s'),
            'modified_by' => session('username') ?? 'system',
        ];
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $userModel->update($id, $updateData);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Get user details for edit modal
     */
    public function get_user()
    {
        $id = $this->request->getGet('id');
        $userModel = new UserModel();

        $user = $userModel->getUserWithRole($id);

        if ($user) {
            return $this->response->setJSON(['success' => true, 'user' => $user]);
        }

        return $this->response->setJSON(['success' => false]);
    }
}
