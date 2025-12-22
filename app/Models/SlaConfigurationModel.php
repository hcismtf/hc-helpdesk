<?php
namespace App\Models;
use CodeIgniter\Model;

class SlaConfigurationModel extends Model
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id', 'priority', 'response_time', 'resolution_time', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
    
}