<?php

namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table = 'role';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'name', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];

    /**
     * Get all permissions associated with this role ID (1 Role -> Many Permissions)
     *
     * @param string|int $roleId
     * @return array
     */
    public function getPermissions($roleId): array
    {
        return $this->db->table('role_permissions')
            ->select('permissions.*')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->orderBy('permissions.name', 'asc')
            ->get()
            ->getResultArray();
    }

    /**
     * Get permission IDs assigned to this role
     *
     * @param string|int $roleId
     * @return array
     */
    public function getPermissionIds($roleId): array
    {
        $rows = $this->db->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_column($rows, 'permission_id');
    }

    /**
     * Sync permissions for a role (Atomic replace in pivot table)
     *
     * @param string|int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function syncPermissions($roleId, array $permissionIds): bool
    {
        $rolePermModel = new RolePermissionsModel();
        
        // Remove existing relations
        $rolePermModel->where('role_id', $roleId)->delete();

        // Insert new relations
        foreach ($permissionIds as $pId) {
            if (!empty($pId)) {
                $rolePermModel->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $pId,
                ]);
            }
        }

        return true;
    }

    /**
     * Get all users assigned to this role (1 Role -> Many Users)
     *
     * @param string|int $roleId
     * @return array
     */
    public function getUsers($roleId): array
    {
        return $this->db->table('users')
            ->select('users.id, users.name, users.email, users.status')
            ->where('users.role_id', $roleId)
            ->groupStart()
                ->where('users.is_deleted !=', 1)
                ->orWhere('users.is_deleted IS NULL')
            ->groupEnd()
            ->get()
            ->getResultArray();
    }
}