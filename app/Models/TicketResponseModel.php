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
        'ticket_id',
        'user_id',
        'author_name',
        'reply',
        'is_internal',
        'status',
        'priority',
        'assigned_to',
        'created_at'
    ];

    /**
     * Ambil seluruh balasan/pesan berdasarkan Ticket ID
     *
     * @param string $ticketId
     * @param string $order
     * @return array
     */
    public function getResponsesByTicketId(string $ticketId, string $order = 'ASC'): array
    {
        return $this->where('ticket_id', $ticketId)
                    ->orderBy('created_at', $order)
                    ->findAll();
    }
}
