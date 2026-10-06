<?php
namespace App\Models;

class SlaModel extends BaseModel
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'priority',
        'response_time',
        'resolution_time',
    ];
}