<?php

namespace App\Models;

use App\Entities\RolePermissionEntity;

class RolePermissionsModel extends BaseModel
{
  protected $table = 'role_permissions';
  protected $primaryKey = 'id';
  protected bool $useAudit = false;
  protected $returnType = RolePermissionEntity::class;
  protected $allowedFields = [
    'role_id',
    'permission_id',
  ];

  // ========================================================
  // RECONSTRUCTED RBAC PIVOT HELPERS
  // ========================================================

  /**
   * Dapatkan seluruh relasi pivot berdasarkan role_id
   *
   * @param string $roleId
   * @return RolePermissionEntity[]
   */
  public function getByRoleId(string $roleId): array
  {
    return $this->where('role_id', $roleId)->findAll();
  }

  /**
   * Dapatkan seluruh relasi pivot berdasarkan permission_id
   *
   * @param string $permissionId
   * @return RolePermissionEntity[]
   */
  public function getByPermissionId(string $permissionId): array
  {
    return $this->where('permission_id', $permissionId)->findAll();
  }
}