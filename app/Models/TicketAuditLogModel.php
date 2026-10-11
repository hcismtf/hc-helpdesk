<?php

namespace App\Models;

use App\Entities\TicketAuditLogEntity;

/**
 * Model Audit Trail Log Tiket
 * Tabel: ticket_audit_log
 */
class TicketAuditLogModel extends BaseModel
{
    protected $table = 'ticket_audit_log';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $returnType = TicketAuditLogEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'ticket_id',
        'user_id',
        'actor_name',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'reason_notes',
    ];

    /**
     * Catat audit log perubahan tiket
     */
    public function logChange(
        string $ticketId,
        string $action,
        ?string $fieldName,
        ?string $oldValue,
        ?string $newValue,
        ?string $reasonNotes = null,
        ?string $userId = null,
        ?string $actorName = null
    ): string {
        $entity = new TicketAuditLogEntity([
            'ticket_id'    => $ticketId,
            'user_id'      => $userId,
            'actor_name'   => $actorName,
            'action'       => $action,
            'field_name'   => $fieldName,
            'old_value'    => $oldValue,
            'new_value'    => $newValue,
            'reason_notes' => $reasonNotes,
            'created_by'   => $actorName,
            'created_date' => date('Y-m-d H:i:s'),
        ]);

        $this->insert($entity);
        return (string) $this->getInsertID();
    }

    /**
     * Ambil riwayat audit log berdasarkan Ticket ID
     */
    public function getLogsByTicketId(string $ticketId, string $order = 'ASC'): array
    {
        return $this->where('ticket_id', $ticketId)
                    ->orderBy('created_date', $order)
                    ->findAll();
    }
}
