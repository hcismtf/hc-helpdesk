<?php
namespace App\Models;

class TiketTransactionsModel extends BaseModel
{
    protected $table = 'tiket_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'tiket_trx_id', 'user_id', 'submitted_by', 'status', 'assigned_to', 'reply', 'created_at'
    ];
}