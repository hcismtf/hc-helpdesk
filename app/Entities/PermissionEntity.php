<?php

namespace App\Entities;

class PermissionEntity extends BaseEntity
{
  protected $casts = [
    'code' => 'string',
    'name' => 'string',
  ];

  public function getCode(): ?string
  {
    return $this->attributes['code'] ?? null;
  }

  public function setCode(?string $code): static
  {
    $this->attributes['code'] = $code;
    return $this;
  }

  public function getName(): ?string
  {
    return $this->attributes['name'] ?? null;
  }

  public function setName(?string $name): static
  {
    $this->attributes['name'] = $name;
    return $this;
  }
}
