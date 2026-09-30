<?php

namespace App\Models;

class PermissionsModel extends BaseModel
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'code',
        'name',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date'
    ];

    /**
     * Get all roles that have this permission
     *
     * @param string|int $permissionId
     * @return array
     */
    public function getRoles($permissionId): array
    {
        return $this->db->table('role_permissions')
            ->select('role.*')
            ->join('role', 'role.id = role_permissions.role_id')
            ->where('role_permissions.permission_id', $permissionId)
            ->get()
            ->getResultArray();
    }
}