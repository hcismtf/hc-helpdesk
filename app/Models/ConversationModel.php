<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversationModel extends Model
{
    protected $table = 'conversation_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ticket_trx_id','ticket_att_id', 'ticket_status', 'ticket_priority', 'nip_encrypted', 'emp_name','email', 'wa_no', 'message', 'created_date','created_by', 'modifed_date','last_response','reply_by', 'file_attachment'];
}