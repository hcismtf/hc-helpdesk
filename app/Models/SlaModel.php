<?php
namespace App\Models;

class SlaModel extends BaseModel
{
    protected $table = 'sla';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'priority',
        'response_time',
        'resolution_time',
    ];
}