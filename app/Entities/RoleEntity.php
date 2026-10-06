<?php

namespace App\Entities;

class RoleEntity extends BaseEntity
{
  protected $casts = [
    'name' => 'string',
  ];
  protected array $permissions = [];

  public function getName(): ?string
  {
    return $this->attributes['name'] ?? null;
  }

  public function setName(?string $name): static
  {
    $this->attributes['name'] = $name;
    return $this;
  }

  public function getPermissions(): array
  {
    return $this->permissions;
  }

  public function setPermissions(array $permissions): static
  {
    $this->permissions = $permissions;
    return $this;
  }

  public function getPermissionCodes(): array
  {
    $codes = [];
    foreach ($this->permissions as $p) {
      if ($p instanceof PermissionEntity) {
        $codes[] = $p->getCode() ?? strtolower(str_replace(' ', '_', (string) $p->getName()));
      } elseif (is_array($p) && !empty($p['code'])) {
        $codes[] = $p['code'];
      }
    }
    return $codes;
  }

  public function hasPermission(string $code): bool
  {
    return in_array($code, $this->getPermissionCodes(), true);
  }
}
