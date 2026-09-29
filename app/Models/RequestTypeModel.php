<?php
namespace App\Models;

class RequestTypeModel extends BaseModel
{
    protected $table = 'request_type';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'name', 'description', 'status', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
}