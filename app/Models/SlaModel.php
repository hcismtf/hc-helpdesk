<?php
namespace App\Models;
use CodeIgniter\Model;

class SlaModel extends Model
{
    protected $table = 'sla_configuration';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'priority',
        'response_time',
        'resolution_time',
        'created_by',
        'created_date',
        'modified_by',
        'modified_date'
    ];
}