<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'email',
        'employee_no',
        'position',
        'position_level',
        'job_title',
        'status',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date',
        'last_login_time',
        'role_id',
        'is_deleted',
        'id'
    ];

    /**
     * Get user along with their 1 assigned Role
     *
     * @param string|int $userId
     * @return array|null
     */
    public function getUserWithRole($userId): ?array
    {
        return $this->db->table($this->table)
            ->select('users.*, role.name as role_name')
            ->join('role', 'role.id = users.role_id', 'left')
            ->where('users.id', $userId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get all active users with their role names
     *
     * @return array
     */
    public function getAllWithRole(): array
    {
        return $this->db->table($this->table)
            ->select('users.*, role.name as role_name')
            ->join('role', 'role.id = users.role_id', 'left')
            ->groupStart()
                ->where('users.is_deleted !=', 1)
                ->orWhere('users.is_deleted IS NULL')
            ->groupEnd()
            ->orderBy('users.created_date', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all permissions granted to a specific user through their 1 assigned role
     *
     * @param string|int $userId
     * @return array Array of permission objects/arrays
     */
    public function getUserPermissions($userId): array
    {
        $user = $this->find($userId);
        if (!$user || empty($user['role_id'])) {
            return [];
        }

        $roleModel = new RoleModel();
        return $roleModel->getPermissions($user['role_id']);
    }

    /**
     * Get list of permission codes (e.g. ['dashboard', 'ticket_list', 'system_settings']) for a user
     *
     * @param string|int $userId
     * @return array
     */
    public function getUserPermissionCodes($userId): array
    {
        $permissions = $this->getUserPermissions($userId);
        $codes = [];

        foreach ($permissions as $p) {
            if (!empty($p['code'])) {
                $codes[] = $p['code'];
            } elseif (!empty($p['name'])) {
                $codes[] = strtolower(str_replace(' ', '_', $p['name']));
            }
        }

        return $codes;
    }

    /**
     * Check if user has a specific permission
     *
     * @param string|int $userId
     * @param string $permissionCode
     * @return bool
     */
    public function hasPermission($userId, string $permissionCode): bool
    {
        $codes = $this->getUserPermissionCodes($userId);
        return in_array($permissionCode, $codes, true);
    }
}