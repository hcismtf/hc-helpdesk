<?php
namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'name',
        'email',
        'password',
        'status',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date',
        'role_id',
        'last_login_time',
        'is_deleted'
    ];
}