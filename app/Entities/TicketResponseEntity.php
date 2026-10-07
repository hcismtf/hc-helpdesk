<?php

namespace App\Entities;

use CodeIgniter\I18n\Time;

/**
 * Entity TicketResponse
 * Merepresentasikan pesan balasan/update tiket pada tabel `ticket_response`
 */
class TicketResponseEntity extends BaseEntity
{
  protected $casts = [
    'ticket_id'   => 'string',
    'user_id'     => '?string',
    'author_name' => '?string',
    'reply'       => 'string',
    'is_internal' => 'boolean',
    'status'      => '?string',
    'priority'    => '?string',
    'assigned_to' => '?string',
  ];

  // --- Getters & Setters ---

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

  public function getAuthorName(): ?string
  {
    return $this->attributes['author_name'] ?? null;
  }

  public function setAuthorName(?string $authorName): self
  {
    $this->attributes['author_name'] = $authorName;
    return $this;
  }

  public function getReply(): ?string
  {
    return $this->attributes['reply'] ?? null;
  }

  public function setReply(string $reply): self
  {
    $this->attributes['reply'] = $reply;
    return $this;
  }

  public function isInternal(): bool
  {
    return (bool) ($this->attributes['is_internal'] ?? false);
  }

  public function setIsInternal(bool $isInternal): self
  {
    $this->attributes['is_internal'] = $isInternal ? 1 : 0;
    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->attributes['status'] ?? null;
  }

  public function setStatus(?string $status): self
  {
    $this->attributes['status'] = $status;
    return $this;
  }

  public function getPriority(): ?string
  {
    return $this->attributes['priority'] ?? null;
  }

  public function setPriority(?string $priority): self
  {
    $this->attributes['priority'] = $priority;
    return $this;
  }

  public function getAssignedTo(): ?string
  {
    return $this->attributes['assigned_to'] ?? null;
  }

  public function setAssignedTo(?string $assignedTo): self
  {
    $this->attributes['assigned_to'] = $assignedTo;
    return $this;
  }
}
