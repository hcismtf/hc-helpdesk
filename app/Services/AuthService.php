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
}
