<?php

namespace App\Models;

/**
 * Model Reply / Response Message Tiket (1-to-Many dari ticket_transactions)
 * Tabel: ticket_response
 */
class TicketResponseModel extends BaseModel
{
    protected $table = 'ticket_response';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id', 'ticket_id', 'user_id', 'submitted_by', 
        'status', 'priority', 'assigned_to', 'reply', 'created_at'
    ];

    /**
     * Ambil seluruh balasan/pesan berdasarkan Ticket ID
     *
     * @param int|string $ticketId
     * @param string $order
     * @return array
     */
    public function getResponsesByTicketId($ticketId, string $order = 'ASC'): array
    {
        return $this->where('ticket_id', $ticketId)
                    ->orderBy('created_at', $order)
                    ->findAll();
    }
}
