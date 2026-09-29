<?php
namespace App\Models;

class RolePermissionsModel extends BaseModel
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'role_id', 'permission_id'
    ];
}