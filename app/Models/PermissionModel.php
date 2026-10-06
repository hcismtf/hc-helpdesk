<?php

namespace App\Models;

use App\Entities\PermissionEntity;
use App\Entities\RoleEntity;

class PermissionModel extends BaseModel
{
  protected $table = 'permissions';
  protected $primaryKey = 'id';
  protected $returnType = PermissionEntity::class;
  protected $allowedFields = [
    'code',
    'name',
  ];

  public function getByCode(string $code): ?PermissionEntity
  {
    return $this->where('code', $code)->first();
  }

  public function getRoles(string $permissionId): array
  {
    return $this->db->table('role_permissions')
      ->select('role.*')
      ->join('role', 'role.id = role_permissions.role_id')
      ->where('role_permissions.permission_id', $permissionId)
      ->get()
      ->getCustomResultObject(RoleEntity::class);
  }
}