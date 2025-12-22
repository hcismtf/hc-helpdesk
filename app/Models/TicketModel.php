<?php
namespace App\Models;
use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table = 'tiket_trx';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'emp_id', 'nip_encrypted', 'emp_name', 'email', 'wa_no', 'req_type', 'subject', 'message',
        'ticket_status', 'ticket_priority', 'created_by', 'created_date', 'modified_by', 'modified_date',
        'due_date', 'first_response_at', 'finish_date'
    ];
}
