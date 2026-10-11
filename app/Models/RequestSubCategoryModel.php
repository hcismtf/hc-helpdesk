<?php

namespace App\Models;

use App\Entities\RequestTypeEntity;

class RequestSubCategoryModel extends BaseModel
{
    protected $table = 'request_sub_category';
    protected $primaryKey = 'id';
    protected $returnType = RequestTypeEntity::class;
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'category_id',
        'name',
        'description',
        'sla_id',
        'badge_tag',
        'is_popular',
        'status',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date',
    ];
}
