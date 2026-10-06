<?php
namespace App\Models;

class SlaConfigurationModel extends BaseModel
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'priority',
        'response_time',
        'resolution_time',
    ];
}