<?php

namespace App\Entities;

class TicketCategoryEntity extends BaseEntity
{
  protected $casts = [
    'code' => 'string',
    'name' => 'string',
    'description' => '?string',
    'is_active' => 'boolean',
  ];

  public function getCode(): ?string
  {
    return $this->attributes['code'] ?? null;
  }

  public function setCode(string $code): self
  {
    $this->attributes['code'] = $code;
    return $this;
  }

  public function getName(): ?string
  {
    return $this->attributes['name'] ?? null;
  }

  public function setName(string $name): self
  {
    $this->attributes['name'] = $name;
    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->attributes['description'] ?? null;
  }

  public function setDescription(?string $description): self
  {
    $this->attributes['description'] = $description;
    return $this;
  }

  public function getIsActive(): ?bool
  {
    return isset($this->attributes['is_active']) ? (bool) $this->attributes['is_active'] : null;
  }

  public function setIsActive(bool $isActive): self
  {
    $this->attributes['is_active'] = $isActive;
    return $this;
  }
}