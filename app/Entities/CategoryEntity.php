<?php

namespace App\Entities;

class CategoryEntity extends BaseEntity
{
    protected $casts = [
        'code'         => 'string',
        'name'         => 'string',
        'description'  => '?string',
        'icon'         => 'string',
        'color'        => 'string',
        'sort_order'   => 'integer',
        'is_active'    => 'boolean',
        'created_by'   => '?string',
        'created_date' => '?string',
        'modified_by'  => '?string',
        'modified_date'=> '?string',
    ];

    public function getName(): string
    {
        return (string) ($this->attributes['name'] ?? '');
    }

    public function setName(string $name): self
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function getCode(): string
    {
        return (string) ($this->attributes['code'] ?? '');
    }

    public function setCode(string $code): self
    {
        $this->attributes['code'] = $code;
        return $this;
    }

    public function getIcon(): string
    {
        return (string) ($this->attributes['icon'] ?? 'fa-solid fa-folder');
    }

    public function setIcon(string $icon): self
    {
        $this->attributes['icon'] = $icon;
        return $this;
    }

    public function getColor(): string
    {
        return (string) ($this->attributes['color'] ?? '#2563EB');
    }

    public function setColor(string $color): self
    {
        $this->attributes['color'] = $color;
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

    public function getSortOrder(): int
    {
        return (int) ($this->attributes['sort_order'] ?? 0);
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->attributes['sort_order'] = $sortOrder;
        return $this;
    }

    public function getIsActive(): bool
    {
        return (bool) ($this->attributes['is_active'] ?? true);
    }

    public function setIsActive(bool $isActive): self
    {
        $this->attributes['is_active'] = $isActive;
        return $this;
    }
}

