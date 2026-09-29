<?php
namespace App\Models;

class TicketModel extends BaseModel
{
    protected $table = 'tiket_trx';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'emp_id', 'nip_encrypted', 'emp_name', 'email', 'wa_no', 'req_type', 'subject', 'message', 'monitoring_url',
        'ticket_status', 'ticket_priority', 'created_by', 'created_date', 'due_date', 'first_response_at', 'finish_date', 'assigned_to', 'modified_by', 'modified_date'
    ];
}
