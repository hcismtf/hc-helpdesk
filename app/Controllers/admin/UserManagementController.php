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
     * Display user management list
     */
    public function user_mgt()
    {
        $roleModel = new RoleModel();
        $roles = $roleModel->orderBy('name', 'asc')->findAll();

        $userModel = new UserModel();
        // Menggunakan clean relationship method getAllWithRole()
        $users = $userModel->getAllWithRole();

        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->orderBy('name', 'asc')->findAll();

        return view('admin/user_mgt', [
            'active' => 'user_mgt',
            'roles' => $roles,
            'users' => $users,
            'permissions' => $permissions
        ]);
    }

    /**
     * Add a new user or restore a soft-deleted user
     */
    public function add_user()
    {
        $userModel = new UserModel();

        $email = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password');
        $roleId = $this->request->getPost('role');
        $name = $this->request->getPost('name');
        $status = $this->request->getPost('status');

        $existingUser = $userModel->where('email', $email)->first();

        if ($existingUser) {
            if (!empty($existingUser['is_deleted']) && (int) $existingUser['is_deleted'] === 1) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $updateData = [
                    'name' => $name,
                    'password' => $hashedPassword,
                    'status' => $status,
                    'role_id' => $roleId,
                    'is_deleted' => 0,
                    'modified_by' => session('username') ?? 'system',
                    'modified_date' => date('Y-m-d H:i:s'),
                ];
                $userModel->update($existingUser['id'], $updateData);

                $user = $userModel->getUserWithRole($existingUser['id']);
                return $this->response->setJSON(['success' => true, 'user' => $user]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Email ' . $email . ' sudah terdaftar dan masih aktif!']);
            }
        }

        $newUserId = $this->authService->generateUUIDv4();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userData = [
            'id' => $newUserId,
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'status' => $status,
            'role_id' => $roleId,
            'is_deleted' => 0,
            'created_by' => session('username') ?? 'system',
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
