<?php
namespace App\Models;
use CodeIgniter\Model;

class RolePermissionsModel extends Model
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'role_id', 'permission_id'
    ];
    
}