<?php
namespace App\Models;

class PermissionsModel extends BaseModel
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'code',
        'name',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date'
    ];
}