<?php
namespace App\Models;

class TicketAttModel extends BaseModel
{
    protected $table = 'tiket_att';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'tiket_trx_id', 'file_name', 'file_path',
        'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
    public $useTimestamps = false;
}
