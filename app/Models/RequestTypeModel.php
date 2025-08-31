<?php
namespace App\Models;
use CodeIgniter\Model;

class RequestTypeModel extends Model
{
    protected $table = 'request_type';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'description',
        'status',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date'
    ];
}