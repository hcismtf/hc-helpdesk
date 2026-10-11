<?php

namespace App\Entities;

/**
 * Entity untuk mencatat audit trail log pada tabel `ticket_audit_log`
 */
class TicketAuditLogEntity extends BaseEntity
{
    protected $attributes = [
        'ticket_id'    => null,
        'user_id'      => null,
        'actor_name'   => null,
        'action'       => null,
        'field_name'   => null,
        'old_value'    => null,
        'new_value'    => null,
        'reason_notes' => null,
    ];

    protected $casts = [
        'ticket_id'    => 'string',
        'user_id'      => '?string',
        'actor_name'   => '?string',
        'action'       => 'string',
        'field_name'   => '?string',
        'old_value'    => '?string',
        'new_value'    => '?string',
        'reason_notes' => '?string',
    ];

    public function getTicketId(): ?string
    {
        return $this->attributes['ticket_id'] ?? null;
    }

    public function setTicketId(string $ticketId): self
    {
        $this->attributes['ticket_id'] = $ticketId;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->attributes['user_id'] ?? null;
    }

    public function setUserId(?string $userId): self
    {
        $this->attributes['user_id'] = $userId;
        return $this;
    }

    public function getActorName(): ?string
    {
        return $this->attributes['actor_name'] ?? null;
    }

    public function setActorName(?string $actorName): self
    {
        $this->attributes['actor_name'] = $actorName;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->attributes['action'] ?? null;
    }

    public function setAction(string $action): self
    {
        $this->attributes['action'] = $action;
        return $this;
    }

    public function getFieldName(): ?string
    {
        return $this->attributes['field_name'] ?? null;
    }

    public function setFieldName(?string $fieldName): self
    {
        $this->attributes['field_name'] = $fieldName;
        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->attributes['old_value'] ?? null;
    }

    public function setOldValue(?string $oldValue): self
    {
        $this->attributes['old_value'] = $oldValue;
        return $this;
    }

    public function getNewValue(): ?string
    {
        return $this->attributes['new_value'] ?? null;
    }

    public function setNewValue(?string $newValue): self
    {
        $this->attributes['new_value'] = $newValue;
        return $this;
    }

    public function getReasonNotes(): ?string
    {
        return $this->attributes['reason_notes'] ?? null;
    }

    public function setReasonNotes(?string $reasonNotes): self
    {
        $this->attributes['reason_notes'] = $reasonNotes;
        return $this;
    }
}
