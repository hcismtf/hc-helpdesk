<?php

namespace App\Models;

/**
 * Model File Attachment Tiket (1-to-Many dari ticket_transactions)
 * Tabel: ticket_attachment
 */
class TicketAttachmentModel extends BaseModel
{
    protected $table = 'ticket_attachment';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    public $useTimestamps = false;

    protected $allowedFields = [
        'ticket_id', 'file_name', 'file_path',
    ];

    /**
     * Ambil seluruh attachment berdasarkan Ticket ID
     *
     * @param int|string $ticketId
     * @return array
     */
    public function getAttachmentsByTicketId($ticketId): array
    {
        return $this->where('ticket_id', $ticketId)->findAll();
    }
}
