<?php
namespace App\Models;
use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
    
}