<?php

namespace App\Models;

use App\Entities\UserEntity;
use App\Entities\RoleEntity;
use App\Entities\PermissionEntity;

class UserModel extends BaseModel
{
  protected $table = 'users';
  protected $primaryKey = 'id';
  protected $returnType = UserEntity::class;
  protected $allowedFields = [
    'name',
    'email',
    'employee_no',
    'position',
    'position_level',
    'job_title',
    'status',
    'last_login_time',
    'role_id',
    'is_deleted',
  ];

  // ========================================================
  // RECONSTRUCTED RBAC: users (role_id) ──> roles ──< role_permissions >── permissions
  // ========================================================

  /**
   * Dapatkan RoleEntity pengguna
   *
   * @param string $userId
   * @return RoleEntity|null
   */
  public function getRole(string $userId): ?RoleEntity
  {
    $user = $this->find($userId);
    if (!$user || empty($user['role_id'])) {
      return null;
    }

    $roleModel = new RoleModel();
    return $roleModel->find($user['role_id']);
  }

  /**
   * Dapatkan seluruh PermissionEntity pengguna melalui rolenya
   *
   * @param string $userId
   * @return PermissionEntity[]
   */
  public function getUserPermissions(string $userId): array
  {
    $user = $this->find($userId);
    if (!$user || empty($user['role_id'])) {
      return [];
    }

    $roleModel = new RoleModel();
    return $roleModel->getPermissions($user['role_id']);
  }

  /**
   * Dapatkan list permission codes milik pengguna
   *
   * @param string $userId
   * @return string[]
   */
  public function getUserPermissionCodes(string $userId): array
  {
    $permissions = $this->getUserPermissions($userId);
    $codes = [];

    foreach ($permissions as $p) {
      if ($p instanceof PermissionEntity) {
        $codes[] = $p->getCode() ?? strtolower(str_replace(' ', '_', (string) $p->getName()));
      } elseif (is_array($p) && !empty($p['code'])) {
        $codes[] = $p['code'];
      }
    }

    return $codes;
  }

  /**
   * Cek apakah user memiliki permission tertentu
   *
   * @param string $userId
   * @param string $permissionCode
   * @return bool
   */
  public function hasPermission(string $userId, string $permissionCode): bool
  {
    return in_array($permissionCode, $this->getUserPermissionCodes($userId), true);
  }

  public function getUserWithRole(string $userId): ?array
  {
    return $this->db->table($this->table)
      ->select('users.*, role.name as role_name')
      ->join('role', 'role.id = users.role_id', 'left')
      ->where('users.id', $userId)
      ->get()
      ->getRowArray();
  }

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
}