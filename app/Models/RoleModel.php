<?php

namespace App\Models;

use App\Entities\RoleEntity;
use App\Entities\PermissionEntity;
use App\Entities\UserEntity;

class RoleModel extends BaseModel
{
  protected $table = 'role';
  protected $primaryKey = 'id';
  protected $returnType = RoleEntity::class;
  protected $allowedFields = [
    'name',
  ];

  public function getPermissions(string $roleId): array
  {
    return $this->db->table('permissions')
      ->select('permissions.*')
      ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
      ->where('role_permissions.role_id', $roleId)
      ->orderBy('permissions.name', 'asc')
      ->get()
      ->getCustomResultObject(PermissionEntity::class);
  }

  public function getPermissionIds(string $roleId): array
  {
    $rows = $this->db->table('role_permissions')
      ->select('permission_id')
      ->where('role_id', $roleId)
      ->get()
      ->getResultArray();

    return array_column($rows, 'permission_id');
  }

  public function getPermissionCodes(string $roleId): array
  {
    $permissions = $this->getPermissions($roleId);
    $codes = [];
    foreach ($permissions as $p) {
      if ($p instanceof PermissionEntity) {
        $codes[] = $p->getCode() ?? strtolower(str_replace(' ', '_', (string) $p->getName()));
      }
    }
    return $codes;
  }

  /**
   * Cek apakah role memiliki permission tertentu berdasarkan code
   */
  public function hasPermission(string $roleId, string $code): bool
  {
    return in_array($code, $this->getPermissionCodes($roleId), true);
  }

  public function syncPermissions(string $roleId, array $permissionIds): bool
  {
    $rolePermModel = new RolePermissionsModel();
    $rolePermModel->where('role_id', $roleId)->delete();

    foreach ($permissionIds as $pId) {
      if (!empty($pId)) {
        $rolePermModel->insert([
          'role_id' => $roleId,
          'permission_id' => (string) $pId,
        ]);
      }
    }

    return true;
  }

  public function getUsers(string $roleId): array
  {
    return $this->db->table('users')
      ->select('users.*')
      ->where('users.role_id', $roleId)
      ->groupStart()
      ->where('users.is_deleted !=', 1)
      ->orWhere('users.is_deleted IS NULL')
      ->groupEnd()
      ->get()
      ->getCustomResultObject(UserEntity::class);
  }
}