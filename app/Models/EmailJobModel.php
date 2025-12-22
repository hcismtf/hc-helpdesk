<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailJobModel extends Model
{
    protected $table = 'email_jobs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['ticket_id', 'recipient_email', 'subject', 'body', 'status', 'created_at', 'sent_at'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    // Get pending emails to send
    public function getPending($limit = 10)
    {
        return $this->where('status', 'pending')
                    ->limit($limit)
                    ->findAll();
    }

    // Mark as sent
    public function markAsSent($id)
    {
        return $this->update($id, [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Mark as failed
    public function markAsFailed($id)
    {
        return $this->update($id, [
            'status' => 'failed'
        ]);
    }
}
