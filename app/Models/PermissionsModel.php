<?php
namespace App\Models;
use CodeIgniter\Model;

class PermissionsModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'code',
        'name',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date'
    ];
}