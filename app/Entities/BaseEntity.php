<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Libraries\AuditorAware;

class BaseEntity extends Entity implements \ArrayAccess
{
  protected $dates = [
    'created_date',
    'modified_date',
  ];

  protected $casts = [
    'id' => 'string',
    'created_by' => 'string',
    'modified_by' => 'string',
  ];

  public function __construct(?array $data = null)
  {
    $this->casts = array_merge([
      'id' => 'string',
      'created_by' => 'string',
      'modified_by' => 'string',
    ], $this->casts);

    $this->dates = array_values(array_unique(array_merge([
      'created_date',
      'modified_date',
    ], $this->dates)));

    parent::__construct($data);

    if (empty($this->attributes['id'])) {
      $this->prePersist();
    }
  }

  public function prePersist(): static
  {
    if (empty($this->attributes['id'])) {
      $this->attributes['id'] = $this->generateUuidV4();
    }

    $auditor = $this->getCurrentAuditor();
    $now = date('Y-m-d H:i:s');

    if (empty($this->attributes['created_by'])) {
      $this->attributes['created_by'] = $auditor;
    }

    if (empty($this->attributes['created_date'])) {
      $this->attributes['created_date'] = $now;
    }

    if (empty($this->attributes['modified_by'])) {
      $this->attributes['modified_by'] = $this->attributes['created_by'];
    }

    if (empty($this->attributes['modified_date'])) {
      $this->attributes['modified_date'] = $this->attributes['created_date'];
    }

    return $this;
  }

  public function preUpdate(): static
  {
    $this->attributes['modified_by'] = $this->getCurrentAuditor();
    $this->attributes['modified_date'] = date('Y-m-d H:i:s');

    return $this;
  }

  public function getCurrentAuditor(): string
  {
    return AuditorAware::getCurrentAuditor();
  }

  public function isNew(): bool
  {
    return empty($this->original['id']);
  }
  protected function generateUuidV4(): string
  {
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
  }

  public function offsetExists(mixed $offset): bool
  {
    return isset($this->attributes[$offset]) || method_exists($this, 'get' . str_replace('_', '', (string) $offset));
  }

  public function offsetGet(mixed $offset): mixed
  {
    return $this->__get($offset);
  }

  public function offsetSet(mixed $offset, mixed $value): void
  {
    $this->__set($offset, $value);
  }

  public function offsetUnset(mixed $offset): void
  {
    unset($this->attributes[$offset]);
  }

  // ==========================================
  // POJO Style Getters & Setters
  // ==========================================

  public function getId(): ?string
  {
    return $this->attributes['id'] ?? null;
  }

  public function setId(?string $id): static
  {
    $this->attributes['id'] = $id;
    return $this;
  }

  public function getCreatedBy(): ?string
  {
    return $this->attributes['created_by'] ?? null;
  }

  public function setCreatedBy(?string $createdBy): static
  {
    $this->attributes['created_by'] = $createdBy;
    return $this;
  }

  public function getCreatedDate(): mixed
  {
    return $this->attributes['created_date'] ?? null;
  }

  public function setCreatedDate(mixed $createdDate): static
  {
    $this->attributes['created_date'] = $createdDate;
    return $this;
  }

  public function getModifiedBy(): ?string
  {
    return $this->attributes['modified_by'] ?? null;
  }

  public function setModifiedBy(?string $modifiedBy): static
  {
    $this->attributes['modified_by'] = $modifiedBy;
    return $this;
  }

  public function getModifiedDate(): mixed
  {
    return $this->attributes['modified_date'] ?? null;
  }

  public function setModifiedDate(mixed $modifiedDate): static
  {
    $this->attributes['modified_date'] = $modifiedDate;
    return $this;
  }
}
