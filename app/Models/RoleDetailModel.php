<?php
namespace App\Models;
use CodeIgniter\Model;

class RoleDetailModel extends Model
{
    protected $table = 'role_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'role_id',
        'user_id',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date'
    ];
}