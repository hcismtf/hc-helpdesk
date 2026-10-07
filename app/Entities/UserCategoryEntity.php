<?php

namespace App\Entities;

class UserCategoryEntity extends BaseEntity
{
  protected $casts = [
    'user_id' => 'string',
    'category_id' => 'string',
  ];

  public function getUserId(): ?string
  {
    return $this->attributes['user_id'] ?? null;
  }

  public function setUserId(string $userId): self
  {
    $this->attributes['user_id'] = $userId;
    return $this;
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
}