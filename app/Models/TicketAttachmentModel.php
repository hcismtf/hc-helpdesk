<?php

namespace App\Models;

use App\Entities\TicketAttachmentEntity;

/**
 * Model File Attachment Tiket (File Pendukung)
 * Tabel: ticket_attachment / ticket_attachments
 */
class TicketAttachmentModel extends BaseModel
{
    protected $table = 'ticket_attachment';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $returnType = TicketAttachmentEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'ticket_id',
        'response_id',
        'comment_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'file_type',
        'created_by',
        'created_date',
        'created_at',
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

    /**
     * Ambil attachment berdasarkan Response ID (ketika diunggah saat membalas)
     *
     * @param string $responseId
     * @return array<TicketAttachmentEntity>
     */
    public function getAttachmentsByResponseId(string $responseId): array
    {
        return $this->where('response_id', $responseId)->findAll();
    }

    /**
     * Alias: Ambil attachment berdasarkan Comment ID
     *
     * @param mixed $commentId
     * @return array<TicketAttachmentEntity>
     */
    public function getAttachmentsByCommentId($commentId): array
    {
        return $this->groupStart()
                    ->where('response_id', (string) $commentId)
                    ->orWhere('comment_id', (string) $commentId)
                    ->groupEnd()
                    ->findAll();
    }
}
