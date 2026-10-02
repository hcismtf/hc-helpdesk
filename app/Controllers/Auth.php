<?php

namespace App\Controllers;

use App\Services\KeycloakService;
use App\Services\SpringHelpdeskService;
use App\Services\UserSyncService;
use App\Models\RoleDetailModel;
use App\Models\RolePermissionsModel;
use App\Models\PermissionsModel;
use App\Models\RoleModel;

class Auth extends BaseController
{
    private KeycloakService $keycloakService;
    private SpringHelpdeskService $helpdeskService;
    private UserSyncService $userSyncService;

    public function __construct()
    {
        $this->keycloakService = new KeycloakService();
        $this->helpdeskService = new SpringHelpdeskService();
        $this->userSyncService = new UserSyncService();
    }

    public function authenticate()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username dan password tidak boleh kosong');
        }

        if ($this->checkSuperadmin($username, $password)) {
            return redirect()->to('/admin/dashboard');
        }

        $tokenData = $this->keycloakService->authenticate($username, $password);
        if (!$tokenData || empty($tokenData['access_token'])) {
            return redirect()->back()->with('error', 'Username atau password Keycloak salah');
        }

        $payload = $this->keycloakService->parseTokenPayload($tokenData['access_token']);
        $employeeNo = $payload['preferred_username'] ?? $username;

        $helpdeskUserDTO = $this->helpdeskService->getUserByEmployeeNo($employeeNo);
        if (!$helpdeskUserDTO) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan di sistem HC');
        }

        $localUser = $this->userSyncService->syncUserHCEazy($employeeNo, $helpdeskUserDTO);
        $this->establishUserSession($localUser, $tokenData);

        return redirect()->to('/admin/dashboard');
    }

    private function checkSuperadmin(string $username, string $password): bool
    {
        $superadminConfig = new \Config\Superadmin();
        if ($username === $superadminConfig->username && $password === $superadminConfig->password_plain) {
            session()->set([
                'isLoggedIn'       => true,
                'role'             => 'superadmin',
                'username'         => $username,
                'user_permissions' => ['dashboard', 'tickets', 'user_management', 'system_settings']
            ]);
            return true;
        }
        return false;
    }

    private function establishUserSession(array $user, array $tokenData): void
    {
        $roleDetailModel = new RoleDetailModel();
        $rolePermissionsModel = new RolePermissionsModel();
        $permissionsModel = new PermissionsModel();
        $roleModel = new RoleModel();

        $roleDetail = $roleDetailModel->where('user_id', $user['id'])->first();
        $roleId = $roleDetail['role_id'] ?? null;

        $userPermissions = [];
        if ($roleId) {
            $rolePerms = $rolePermissionsModel->where('role_id', $roleId)->findAll();
            foreach ($rolePerms as $rp) {
                $perm = $permissionsModel->find($rp['permission_id']);
                if ($perm) {
                    $userPermissions[] = $perm['code'];
                }
            }
        }

        $roleObj = $roleId ? $roleModel->find($roleId) : null;
        $userRoleName = $roleObj['name'] ?? 'User';

        session()->set([
            'isLoggedIn'       => true,
            'role'             => $userRoleName,
            'role_name'        => $userRoleName,
            'username'         => $user['name'],
            'user_id'          => $user['id'],
            'employee_no'      => $user['employee_no'],
            'user_permissions' => $userPermissions,
            'access_token'     => $tokenData['access_token'],
            'refresh_token'    => $tokenData['refresh_token'] ?? null,
        ]);
    }
}