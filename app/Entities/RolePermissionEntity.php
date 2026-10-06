<?php

namespace App\Entities;

class RolePermissionEntity extends BaseEntity
{
  protected $casts = [
    'role_id'       => 'string',
    'permission_id' => 'string',
  ];

  public function getRoleId(): ?string
  {
    return $this->attributes['role_id'] ?? null;
  }

  public function setRoleId(?string $roleId): static
  {
    $this->attributes['role_id'] = $roleId;
    return $this;
  }

  public function getPermissionId(): ?string
  {
    return $this->attributes['permission_id'] ?? null;
  }

  public function setPermissionId(?string $permissionId): static
  {
    $this->attributes['permission_id'] = $permissionId;
    return $this;
  }
}
