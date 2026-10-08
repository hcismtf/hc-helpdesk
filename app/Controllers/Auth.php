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
            if (\App\Services\AuthService::canAccessAdmin()) {
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

        // Encrypt sensitive tokens (access_token, refresh_token, app_jwt)
        $encryptedAccessToken = \App\Services\AuthService::encryptToken($accessToken);
        $encryptedRefreshToken = \App\Services\AuthService::encryptToken($refreshToken);
        $encryptedAppJwt = \App\Services\AuthService::encryptToken($appJwt);

        // 6. Set CodeIgniter session with encrypted tokens
        session()->set([
            'isLoggedIn' => true,
            'access_token' => $encryptedAccessToken,
            'refresh_token' => $encryptedRefreshToken,
            'app_jwt' => $encryptedAppJwt,
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

        // Determine redirect: if user is admin with authorized role, redirect to admin dashboard, otherwise stay on portal
        $isAdmin = \App\Services\AuthService::canAccessAdmin();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login berhasil! Selamat datang, ' . esc($localUser['name']),
            'access_token' => $encryptedAccessToken,
            'refresh_token' => $encryptedRefreshToken,
            'app_jwt' => $encryptedAppJwt,
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
        $rawRefreshToken = $this->request->getPost('refresh_token') ?? session('refresh_token');

        if (empty($rawRefreshToken)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Refresh token tidak ditemukan.'
            ]);
        }

        // Decrypt refresh token before calling Keycloak service
        $plainRefreshToken = \App\Services\AuthService::decryptToken($rawRefreshToken);

        $tokenData = $this->keycloakService->refreshToken($plainRefreshToken);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui token Keycloak. Silakan login kembali.'
            ]);
        }

        // Encrypt renewed tokens
        $encryptedAccessToken = \App\Services\AuthService::encryptToken($tokenData['access_token']);
        $newRefreshToken = $tokenData['refresh_token'] ?? $plainRefreshToken;
        $encryptedRefreshToken = \App\Services\AuthService::encryptToken($newRefreshToken);

        // Update session with encrypted tokens
        session()->set('access_token', $encryptedAccessToken);
        session()->set('refresh_token', $encryptedRefreshToken);

        return $this->response->setJSON([
            'status' => 'success',
            'access_token' => $encryptedAccessToken,
            'refresh_token' => $encryptedRefreshToken,
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
