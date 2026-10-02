<?php
namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    // protected $allowedFields = [
    //     'id',
    //     'name',
    //     'email',
    //     'password',
    //     'status',
    //     'created_by',
    //     'created_date',
    //     'modified_by',
    //     'modified_date',
    //     'role_id',
    //     'last_login_time',
    //     'is_deleted'
    // ];

    protected $allowedFields = [
        'name',
        'email',
        'employee_no',
        'status',
        'position',
        'position_level',
        'job_title',        
        'is_deleted',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date',
        'last_login_time',
        'role_id',
        'id'
    ];
}