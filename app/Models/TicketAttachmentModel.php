<?php

namespace App\Models;

use App\Entities\TicketAttachmentEntity;

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
    protected $returnType = TicketAttachmentEntity::class;
    protected $useSoftDeletes = false;
    public $useTimestamps = false;

    protected $allowedFields = [
        'ticket_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
    ];

    /**
     * Ambil seluruh attachment berdasarkan Ticket ID
     *
     * @param int|string $ticketId
     * @return array<TicketAttachmentEntity>
     */
    public function getAttachmentsByTicketId($ticketId): array
    {
        return $this->where('ticket_id', (string) $ticketId)->findAll();
    }
}
