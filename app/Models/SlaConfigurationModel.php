<?php
namespace App\Models;

class SlaConfigurationModel extends BaseModel
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'priority', 'response_time', 'resolution_time', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
}