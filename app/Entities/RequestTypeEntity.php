<?php

namespace App\Entities;

class RequestTypeEntity extends BaseEntity
{
  protected $casts = [
    'category_id' => 'string',
    'code' => 'string',
    'name' => 'string',
    'default_sla_id' => '?string',
    'requires_approval' => 'boolean',
    'is_active' => 'boolean',
  ];

  public function determineInitialStatus(): string
  {
    return $this->requires_approval ? 'PENDING_APPROVAL' : 'OPEN';
  }

  public function getCategoryId(): ?string
  {
    return $this->attributes['category_id'] ?? null;
  }

  public function setCategoryId(string $categoryId): self
  {
    $this->attributes['category_id'] = $categoryId;
    return $this;
  }

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

  public function getDefaultSlaId(): ?string
  {
    return $this->attributes['default_sla_id'] ?? null;
  }

  public function setDefaultSlaId(?string $defaultSlaId): self
  {
    $this->attributes['default_sla_id'] = $defaultSlaId;
    return $this;
  }

  public function getRequiresApproval(): bool
  {
    return (bool) ($this->attributes['requires_approval'] ?? false);
  }

  public function setRequiresApproval(bool $requiresApproval): self
  {
    $this->attributes['requires_approval'] = $requiresApproval;
    return $this;
  }

  public function getIsActive(): bool
  {
    return (bool) ($this->attributes['is_active'] ?? false);
  }

  public function setIsActive(bool $isActive): self
  {
    $this->attributes['is_active'] = $isActive;
    return $this;
  }
}