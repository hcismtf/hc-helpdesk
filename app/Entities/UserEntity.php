<?php

namespace App\Entities;

class UserEntity extends BaseEntity
{
  protected $casts = [
    'name' => 'string',
    'email' => 'string',
    'employee_no' => 'string',
    'position' => '?string',
    'position_level' => '?string',
    'job_title' => '?string',
    'status' => 'string',
    'last_login_time' => '?datetime',
    'role_id' => '?string',
    'category_id' => '?string',
    'is_deleted' => 'boolean',
  ];

  protected ?RoleEntity $role = null;

  public function getName(): ?string
  {
    return $this->attributes['name'] ?? null;
  }

  public function setName(?string $name): static
  {
    $this->attributes['name'] = $name;
    return $this;
  }

  public function getEmail(): ?string
  {
    return $this->attributes['email'] ?? null;
  }

  public function setEmail(?string $email): static
  {
    $this->attributes['email'] = $email;
    return $this;
  }

  public function getEmployeeNo(): ?string
  {
    return $this->attributes['employee_no'] ?? null;
  }

  public function setEmployeeNo(?string $employeeNo): static
  {
    $this->attributes['employee_no'] = $employeeNo;
    return $this;
  }

  public function getPosition(): ?string
  {
    return $this->attributes['position'] ?? null;
  }

  public function setPosition(?string $position): static
  {
    $this->attributes['position'] = $position;
    return $this;
  }

  public function getPositionLevel(): ?string
  {
    return $this->attributes['position_level'] ?? null;
  }

  public function setPositionLevel(?string $positionLevel): static
  {
    $this->attributes['position_level'] = $positionLevel;
    return $this;
  }

  public function getJobTitle(): ?string
  {
    return $this->attributes['job_title'] ?? null;
  }

  public function setJobTitle(?string $jobTitle): static
  {
    $this->attributes['job_title'] = $jobTitle;
    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->attributes['status'] ?? null;
  }

  public function setStatus(?string $status): static
  {
    $this->attributes['status'] = $status;
    return $this;
  }

  public function getLastLoginTime(): mixed
  {
    return $this->attributes['last_login_time'] ?? null;
  }

  public function setLastLoginTime(mixed $lastLoginTime): static
  {
    $this->attributes['last_login_time'] = $lastLoginTime;
    return $this;
  }

  public function getRoleId(): ?string
  {
    return $this->attributes['role_id'] ?? null;
  }

  public function setRoleId(?string $roleId): static
  {
    $this->attributes['role_id'] = $roleId;
    return $this;
  }

  public function getCategoryId(): ?string
  {
    return $this->attributes['category_id'] ?? null;
  }

  public function setCategoryId(?string $roleId): static
  {
    $this->attributes['category_id'] = $roleId;
    return $this;
  }

  public function getIsDeleted(): bool
  {
    return (bool) ($this->attributes['is_deleted'] ?? false);
  }

  public function setIsDeleted(bool $isDeleted): static
  {
    $this->attributes['is_deleted'] = $isDeleted;
    return $this;
  }

  // ==========================================
  // RBAC Relations & Helper Methods
  // users (role_id) ──> roles ──< role_permissions >── permissions
  // ==========================================

  public function getRole(): ?RoleEntity
  {
    return $this->role;
  }

  public function setRole(?RoleEntity $role): static
  {
    $this->role = $role;
    if ($role) {
      $this->setRoleId($role->getId());
    }
    return $this;
  }

  public function getPermissions(): array
  {
    return $this->role ? $this->role->getPermissions() : [];
  }

  public function getPermissionCodes(): array
  {
    return $this->role ? $this->role->getPermissionCodes() : [];
  }

  public function hasPermission(string $code): bool
  {
    return $this->role ? $this->role->hasPermission($code) : false;
  }

  public function managesCategory(?string $categoryId): bool
  {
    if ($this->category_id === null || $categoryId === null) {
      return false;
    }

    return (string) $this->category_id === (string) $categoryId;
  }

  public function isActive(): bool
  {
    $status = strtolower((string) ($this->attributes['status'] ?? ''));
    return ($status === 'active' || $status === '1') && !$this->isDeleted();
  }

  public function isDeleted(): bool
  {
    return !empty($this->attributes['is_deleted']);
  }
}
