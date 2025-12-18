<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'email', 'password', 'status', 'created_by', 'created_date',
        'modified_by', 'modified_date', 'role_id', 'last_login_time'
    ];
    
}