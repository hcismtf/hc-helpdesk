<?php
namespace App\Models;
use CodeIgniter\Model;

class TicketAttModel extends Model
{
    protected $table = 'tiket_att';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tiket_trx_id','file_name','file_path',
        'created_by','created_date','modified_by','modified_date'
    ];
    public $useTimestamps = false;
}
