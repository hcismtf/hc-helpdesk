<?php

namespace App\Entities;

class SlaEntity extends BaseEntity
{
  protected $casts = [
    'name' => 'string',
    'resolution_time_hours' => 'integer',
    'response_time_hours' => '?integer',
    'description' => '?string',
    'is_active' => 'boolean',
  ];

  public function getName(): ?string
  {
    return $this->attributes['name'] ?? null;
  }

  public function setName(string $name): self
  {
    $this->attributes['name'] = $name;
    return $this;
  }

  public function getResolutionTimeHours(): ?int
  {
    return isset($this->attributes['resolution_time_hours']) ? (int) $this->attributes['resolution_time_hours'] : null;
  }

  public function setResolutionTimeHours(int $resolutionTimeHours): self
  {
    $this->attributes['resolution_time_hours'] = $resolutionTimeHours;
    return $this;
  }

  public function getResponseTimeHours(): ?int
  {
    return isset($this->attributes['response_time_hours']) ? (int) $this->attributes['response_time_hours'] : null;
  }

  public function setResponseTimeHours(?int $responseTimeHours): self
  {
    $this->attributes['response_time_hours'] = $responseTimeHours;
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