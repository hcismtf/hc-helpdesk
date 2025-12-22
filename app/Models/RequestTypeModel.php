<?php
namespace App\Models;
use CodeIgniter\Model;

class RequestTypeModel extends Model
{
    protected $table = 'request_type';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id', 'name', 'description', 'status', 'created_by', 'created_date', 'modified_by', 'modified_date'];
    protected $useTimestamps = false;
    
}