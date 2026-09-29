<?php
namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table = 'role';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'name', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
}