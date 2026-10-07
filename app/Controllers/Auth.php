<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Services\Auth\KeycloackAuthService;

class Auth extends BaseController
{
    protected KeycloackAuthService $keycloakService;

    public function __construct()
    {
        $this->keycloakService = new KeycloackAuthService();
    }

    /**
     * Fallback login view (redirects to / or shows modal)
     */
    public function login()
    {
        if (session('isLoggedIn')) {
            if (session('role') === 'superadmin' || !empty(session('user_permissions'))) {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/');
        }
        return redirect()->to('/');
    }

    /**
     * AJAX Login for normal employees from modal on /
     */
    public function ajaxLogin()
    {
        $username = trim($this->request->getPost('username') ?? $this->request->getPost('employee_no') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (empty($username) || empty($password)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Username (NIP) dan kata sandi wajib diisi.'
            ]);
        }

        // 1. Authenticate with Keycloak via Direct Access Grant (Password)
        // Note: Keycloak username IS employee_no (NIP), NOT email!
        $tokenData = $this->keycloakService->authenticate($username, $password);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Username atau Kata Sandi salah.'
            ]);
        }

        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'] ?? null;
        $tokenPayload = $this->keycloakService->parseTokenPayload($accessToken);

        $employeeNo = $username;

        // 2. Query Helpdesk API (http://127.0.0.1:9092/api/helpdesk/user?employeeNo=...)
        $helpdeskUser = $this->keycloakService->getHelpdeskUserData($employeeNo);

        $userModel = new UserModel();
        $localUser = $userModel->where('employee_no', $employeeNo)->first();

        $fullName = $helpdeskUser['name']
            ?? $helpdeskUser['fullName']
            ?? ($tokenPayload['name'] ?? ($tokenPayload['preferred_username'] ?? $employeeNo));

        $email = $helpdeskUser['officeMail']
            ?? ($tokenPayload['email'] ?? null);

        if (!$localUser && !empty($email)) {
            $localUser = $userModel->where('email', $email)->first();
        }

        $position = $helpdeskUser['position'] ?? null;
        $positionLevel = $helpdeskUser['positionLevel'] ?? ($helpdeskUser['position_level'] ?? null);
        $jobTitle = $helpdeskUser['jobTitle'] ?? ($helpdeskUser['job_title'] ?? null);

        // 3. Upsert into users table
        if (!$localUser) {
            // Create new user
            $newUserData = [
                'employee_no' => $employeeNo,
                'name' => $fullName,
                'email' => $email,
                'position' => $position,
                'position_level' => $positionLevel,
                'job_title' => $jobTitle,
                'status' => 'ACTIVE',
                'last_login_time' => date('Y-m-d H:i:s'),
                'is_deleted' => 0,
                'role_id' => $helpdeskUser['role_id'] ?? null,
            ];

            $userId = $userModel->insert($newUserData, true);
            $localUser = $userModel->find($userId);
        } else {
            // Update only if data changed
            $updateData = [];
            if ($localUser['employee_no'] !== $employeeNo) {
                $updateData['employee_no'] = $employeeNo;
            }
            if (!empty($fullName) && $localUser['name'] !== $fullName) {
                $updateData['name'] = $fullName;
            }
            if (!empty($email) && $localUser['email'] !== $email) {
                $updateData['email'] = $email;
            }
            if (!empty($position) && ($localUser['position'] ?? null) !== $position) {
                $updateData['position'] = $position;
            }
            if (!empty($positionLevel) && ($localUser['position_level'] ?? null) !== $positionLevel) {
                $updateData['position_level'] = $positionLevel;
            }
            if (!empty($jobTitle) && ($localUser['job_title'] ?? null) !== $jobTitle) {
                $updateData['job_title'] = $jobTitle;
            }

            $updateData['last_login_time'] = date('Y-m-d H:i:s');
            $updateData['is_deleted'] = 0;

            $userModel->update($localUser['id'], $updateData);
            $localUser = $userModel->find($localUser['id']);
        }

        // 4. Role & Permissions (if any assigned)
        $userWithRole = $userModel->getUserWithRole($localUser['id']);
        $permissionCodes = $userModel->getUserPermissionCodes($localUser['id']);
        $roleName = $userWithRole['role_name'] ?? 'USER';

        // 5. Generate signed JWT token for local application session & API authorization
        $appJwt = $this->createAppJwt([
            'sub' => $localUser['id'],
            'employee_no' => $employeeNo,
            'name' => $localUser['name'],
            'email' => $localUser['email'],
            'role' => $roleName,
            'iat' => time(),
            'exp' => time() + (8 * 3600), // 8 hours
        ]);

        // 6. Set CodeIgniter session
        session()->set([
            'isLoggedIn' => true,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'app_jwt' => $appJwt,
            'user_id' => $localUser['id'],
            'employee_no' => $employeeNo,
            'username' => $employeeNo,
            'name' => $localUser['name'],
            'email' => $localUser['email'],
            'position' => $localUser['position'] ?? $position,
            'role_id' => $localUser['role_id'],
            'role' => $roleName,
            'role_name' => $roleName,
            'helpdesk_data' => $helpdeskUser,
            'user_permissions' => $permissionCodes,
        ]);

        // Determine redirect: if user is admin with dashboard permission, can give link, otherwise stay on portal
        $isAdmin = (strtolower($roleName) === 'superadmin' || in_array('dashboard', $permissionCodes, true));

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login berhasil! Selamat datang, ' . esc($localUser['name']),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'app_jwt' => $appJwt,
            'is_admin' => $isAdmin,
            'redirect' => $isAdmin ? base_url('admin/dashboard') : base_url('/'),
            'user' => [
                'id' => $localUser['id'],
                'employee_no' => $employeeNo,
                'name' => $localUser['name'],
                'email' => $localUser['email'],
                'position' => $localUser['position'] ?? '',
                'role' => $roleName,
            ]
        ]);
    }

    /**
     * Refresh Keycloak token endpoint
     */
    public function refreshToken()
    {
        $refreshToken = $this->request->getPost('refresh_token') ?? session('refresh_token');

        if (empty($refreshToken)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Refresh token tidak ditemukan.'
            ]);
        }

        $tokenData = $this->keycloakService->refreshToken($refreshToken);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui token Keycloak. Silakan login kembali.'
            ]);
        }

        // Update session
        session()->set('access_token', $tokenData['access_token']);
        if (!empty($tokenData['refresh_token'])) {
            session()->set('refresh_token', $tokenData['refresh_token']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? $refreshToken,
        ]);
    }

    /**
     * User logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    /**
     * Helper to sign a local HS256 JWT
     */
    private function createAppJwt(array $payload): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payloadJson = json_encode($payload);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));

        $secret = config('Encryption')->key ?? 'mtf-hc-helpdesk-app-secret-jwt-key-2026';
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
