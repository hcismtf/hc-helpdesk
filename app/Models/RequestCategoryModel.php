<?php

namespace App\Models;

use App\Entities\CategoryEntity;

class RequestCategoryModel extends BaseModel
{
    protected $table = 'request_category';
    protected $primaryKey = 'id';
    protected $returnType = CategoryEntity::class;
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'code',
        'name',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date',
    ];

    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->findAll();
    }
}
