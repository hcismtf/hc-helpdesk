<?php
namespace App\Models;

use CodeIgniter\Model;

class ReportJobModel extends Model
{
    protected $table      = 'report_jobs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id', 'report_type', 'filter_params', 'file_path', 'status', 'action',
        'created_at', 'updated_at', 'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}