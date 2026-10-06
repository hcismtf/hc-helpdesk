<?php
namespace App\Models;

class RequestTypeModel extends BaseModel
{
    protected $table = 'request_type';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'description',
        'status',
    ];
}