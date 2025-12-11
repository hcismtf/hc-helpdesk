<?php
namespace App\Models;
use CodeIgniter\Model;

class TiketTransactionsModel extends Model
{
    protected $table = 'tiket_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tiket_trx_id', 'user_id', 'submitted_by', 'status', 'assigned_to', 'reply','created_at'
    ];
}