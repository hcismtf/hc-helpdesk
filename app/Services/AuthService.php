<?php

namespace App\Services;

use App\Models\UserModel;

class AuthService
{
    /**
     * Generate UUID v4 format
     * Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     *
     * @return string
     */
    public function generateUUIDv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variant 1
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Generate UUID v4 from password input (deterministic)
     *
     * @param string $password
     * @return string
     */
    public function generateUUIDFromPassword(string $password): string
    {
        $hash = md5($password, true);
        $hash[6] = chr((ord($hash[6]) & 0x0f) | 0x40);
        $hash[8] = chr((ord($hash[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($hash), 4));
    }

    /**
     * Verify user password and upgrade legacy hash if needed
     *
     * @param string $password
     * @param array $user
     * @param UserModel $userModel
     * @return bool
     */
    public function verifyAndUpgradePassword(string $password, array $user, UserModel $userModel): bool
    {
        if (password_verify($password, $user['password'])) {
            return true;
        }

        // Fallback for legacy UUID-based password hash
        if ($this->generateUUIDFromPassword($password) === $user['password']) {
            $userModel->update($user['id'], [
                'password' => password_hash($password, PASSWORD_BCRYPT)
            ]);
            return true;
        }

        return false;
    }

    /**
     * Get role name and permission codes for a given user ID
     * (1 User has 1 Role, 1 Role has Many Permissions)
     *
     * @param mixed $userId
     * @return array ['role_id' => string|null, 'role_name' => string, 'permissions' => array]
     */
    public function getUserRoleAndPermissions($userId): array
    {
        $userModel = new UserModel();
        $user = $userModel->getUserWithRole($userId);

        if (!$user) {
            return [
                'role_id' => null,
                'role_name' => 'User',
                'permissions' => [],
            ];
        }

        $roleId = $user['role_id'] ?? null;
        $roleName = $user['role_name'] ?? 'User';

        // Get permissions from UserModel relationship method
        $permissionCodes = $userModel->getUserPermissionCodes($userId);

        return [
            'role_id' => $roleId,
            'role_name' => $roleName,
            'permissions' => $permissionCodes,
        ];
    }

    /**
     * Memeriksa apakah user saat ini memiliki hak akses administrator untuk mengakses /admin/*
     * 
     * Syarat keamanan:
     * 1. User wajib login (isLoggedIn === true).
     * 2. Superadmin selalu memiliki akses penuh (via role 'Super Admin' atau config superadmin).
     * 3. Wajib memiliki role_id yang valid (tidak boleh null, kosong, atau 0).
     * 4. Role BUKAN role non-administratif (seperti 'Employee', 'User', 'Guest', 'Pelapor').
     * 5. Role merupakan role staf/admin MTF HC Helpdesk (Super Admin, Department Head, Helpdesk L1, PIC Dev / BA, dll)
     *    atau memiliki setidaknya salah satu hak akses administratif.
     *
     * @param array|null $sessionData
     * @return bool
     */
    public static function canAccessAdmin(?array $sessionData = null): bool
    {
        $session = $sessionData;
        if ($session === null) {
            if (!function_exists('session')) {
                return false;
            }
            $session = session()->get();
        }

        if (empty($session['isLoggedIn'])) {
            return false;
        }

        $roleId = $session['role_id'] ?? null;
        $roleName = (string) ($session['role'] ?? $session['role_name'] ?? '');
        $username = (string) ($session['username'] ?? $session['employee_no'] ?? '');
        $permissions = $session['user_permissions'] ?? $session['permissions'] ?? [];

        // 1. Superadmin check (role name, atau superadmin config)
        $cleanRole = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]/', '', $roleName)));
        if ($cleanRole === 'superadmin') {
            return true;
        }

        try {
            if (function_exists('config')) {
                $superConfig = config('Superadmin');
                if (!empty($superConfig->username) && strtolower($username) === strtolower($superConfig->username)) {
                    return true;
                }
            } elseif (class_exists(\Config\Superadmin::class)) {
                $superConfig = new \Config\Superadmin();
                if (!empty($superConfig->username) && strtolower($username) === strtolower($superConfig->username)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika konfigurasi superadmin tidak tersedia di lingkungan eksekusi
        }

        // 2. Wajib memiliki role_id yang valid
        if (empty($roleId)) {
            return false;
        }

        // 3. Blacklist role non-admin (misal 'employee', 'user', 'guest', 'pelapor')
        $nonAdminRoles = ['employee', 'user', 'karyawan', 'guest', 'pelapor'];
        if (in_array($cleanRole, $nonAdminRoles, true)) {
            return false;
        }

        // 4. Whitelist role administratif MTF HC Helpdesk
        $allowedAdminRoles = [
            'superadmin',
            'departmenthead',
            'helpdeskl1',
            'helpdeskl2',
            'picdevba',
            'picdev',
            'administrator',
            'admin',
            'staff',
            'officer'
        ];
        if (in_array($cleanRole, $allowedAdminRoles, true)) {
            return true;
        }

        // 5. Cek jika memiliki permission administratif di sistem
        $adminPermKeywords = [
            'ticket:read', 'ticket:update', 'ticket:reply', 'ticket:status', 'ticket:delete',
            'settings:read', 'settings.read', 'role:read', 'role.read', 'role:create', 'role:update', 'role:delete',
            'user:read', 'user.read', 'user:create', 'user:update', 'user:delete',
            'sla:read', 'sla.read', 'sla:create', 'sla:update', 'sla:delete',
            'request_type:read', 'request_type.read', 'report:read', 'report.read', 'report:export',
            'dev:access', 'dev.access', 'dashboard', 'tickets', 'system_settings', 'user_management', 'reports'
        ];
        foreach ($adminPermKeywords as $keyword) {
            if (in_array($keyword, $permissions, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mengenkripsi token sensitif (access_token, refresh_token, jwt/app_jwt)
     * Menggunakan CodeIgniter 4 Encrypter (OpenSSL AES-256)
     *
     * @param string|null $plainText
     * @return string|null Base64-encoded encrypted string
     */
    public static function encryptToken(?string $plainText): ?string
    {
        if ($plainText === null || $plainText === '') {
            return null;
        }

        try {
            $encrypter = \Config\Services::encrypter();
            $encryptedRaw = $encrypter->encrypt($plainText);
            return base64_encode($encryptedRaw);
        } catch (\Throwable $e) {
            log_message('error', 'Token encryption error: ' . $e->getMessage());
            return $plainText;
        }
    }

    /**
     * Mendekripsi token terenkripsi kembali ke plaintext asli
     *
     * @param string|null $cipherText Base64-encoded ciphertext
     * @return string|null Plaintext token
     */
    public static function decryptToken(?string $cipherText): ?string
    {
        if ($cipherText === null || $cipherText === '') {
            return null;
        }

        try {
            $rawBinary = base64_decode($cipherText, true);
            if ($rawBinary === false) {
                return $cipherText;
            }

            $encrypter = \Config\Services::encrypter();
            return $encrypter->decrypt($rawBinary);
        } catch (\Throwable $e) {
            // Fallback aman jika token sudah dalam format plaintext
            return $cipherText;
        }
    }
}
