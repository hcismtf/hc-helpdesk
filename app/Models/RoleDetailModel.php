<?php
namespace App\Models;

class RoleDetailModel extends BaseModel
{
    protected $table = 'role_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'role_id', 'user_id', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
}