<?php

namespace App\Entities;

use CodeIgniter\I18n\Time;

class TicketEntity extends BaseEntity
{
  protected $dates = [
    'response_due_date',
    'resolution_due_date',

    'response_date',
    'completed_date',
    'done_date',

    'on_hold_date',
    're_response_date',
    're_completed_date',
  ];

  protected $casts = [
    'ticket_no' => 'string',
    'request_type_id' => 'string',
    'sla_id' => 'string',
    'title' => 'string',
    'description' => 'string',
    'status' => 'string',

    'pic_helpdesk_id' => '?string',
    'pic_dev_ba_id' => '?string',

    'rejection_notes' => '?string',

    'total_hours' => '?float',
    'sla_status' => '?string',
    'sla_percentage' => '?float',
  ];

  // --- Attributes Getter & Setter ---

  public function getTicketNo(): ?string
  {
    return $this->attributes['ticket_no'] ?? null;
  }

  public function setTicketNo(string $ticketNo): self
  {
    $this->attributes['ticket_no'] = $ticketNo;
    return $this;
  }

  public function getRequestTypeId(): ?string
  {
    return $this->attributes['request_type_id'] ?? null;
  }

  public function setRequestTypeId(string $requestTypeId): self
  {
    $this->attributes['request_type_id'] = $requestTypeId;
    return $this;
  }

  public function getSlaId(): ?string
  {
    return $this->attributes['sla_id'] ?? null;
  }

  public function setSlaId(string $slaId): self
  {
    $this->attributes['sla_id'] = $slaId;
    return $this;
  }

  public function getTitle(): ?string
  {
    return $this->attributes['title'] ?? null;
  }

  public function setTitle(string $title): self
  {
    $this->attributes['title'] = $title;
    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->attributes['description'] ?? null;
  }

  public function setDescription(string $description): self
  {
    $this->attributes['description'] = $description;
    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->attributes['status'] ?? null;
  }

  public function setStatus(string $status): self
  {
    $this->attributes['status'] = $status;
    return $this;
  }

  public function getPicHelpdeskId(): ?string
  {
    return $this->attributes['pic_helpdesk_id'] ?? null;
  }

  public function setPicHelpdeskId(?string $picHelpdeskId): self
  {
    $this->attributes['pic_helpdesk_id'] = $picHelpdeskId;
    return $this;
  }

  public function getPicDevBaId(): ?string
  {
    return $this->attributes['pic_dev_ba_id'] ?? null;
  }

  public function setPicDevBaId(?string $picDevBaId): self
  {
    $this->attributes['pic_dev_ba_id'] = $picDevBaId;
    return $this;
  }

  public function getRejectionNotes(): ?string
  {
    return $this->attributes['rejection_notes'] ?? null;
  }

  public function setRejectionNotes(?string $rejectionNotes): self
  {
    $this->attributes['rejection_notes'] = $rejectionNotes;
    return $this;
  }

  public function getTotalHours(): ?float
  {
    return isset($this->attributes['total_hours']) ? (float) $this->attributes['total_hours'] : null;
  }

  public function setTotalHours(?float $totalHours): self
  {
    $this->attributes['total_hours'] = $totalHours;
    return $this;
  }

  public function getSlaStatus(): ?string
  {
    return $this->attributes['sla_status'] ?? null;
  }

  public function setSlaStatus(?string $slaStatus): self
  {
    $this->attributes['sla_status'] = $slaStatus;
    return $this;
  }

  public function getSlaPercentage(): ?float
  {
    return isset($this->attributes['sla_percentage']) ? (float) $this->attributes['sla_percentage'] : null;
  }

  public function setSlaPercentage(?float $slaPercentage): self
  {
    $this->attributes['sla_percentage'] = $slaPercentage;
    return $this;
  }

  // --- Date Properties Getter & Setter ---

  public function getResponseDueDate(): ?Time
  {
    return isset($this->attributes['response_due_date'])
      ? $this->mutateDate($this->attributes['response_due_date'])
      : null;
  }

  public function setResponseDueDate(mixed $responseDueDate): self
  {
    $this->attributes['response_due_date'] = $responseDueDate;
    return $this;
  }

  public function getResolutionDueDate(): ?Time
  {
    return $this->dates['resolution_due_date'] ?? $this->mutateDate($this->attributes['resolution_due_date'] ?? null);
  }

  public function setResolutionDueDate(mixed $resolutionDueDate): self
  {
    $this->attributes['resolution_due_date'] = $resolutionDueDate;
    return $this;
  }

  public function getResponseDate(): ?Time
  {
    return $this->dates['response_date'] ?? $this->mutateDate($this->attributes['response_date'] ?? null);
  }

  public function setResponseDate(mixed $responseDate): self
  {
    $this->attributes['response_date'] = $responseDate;
    return $this;
  }

  public function getCompletedDate(): ?Time
  {
    return $this->dates['completed_date'] ?? $this->mutateDate($this->attributes['completed_date'] ?? null);
  }

  public function setCompletedDate(mixed $completedDate): self
  {
    $this->attributes['completed_date'] = $completedDate;
    return $this;
  }

  public function getDoneDate(): ?Time
  {
    return $this->dates['done_date'] ?? $this->mutateDate($this->attributes['done_date'] ?? null);
  }

  public function setDoneDate(mixed $doneDate): self
  {
    $this->attributes['done_date'] = $doneDate;
    return $this;
  }

  public function getOnHoldDate(): ?Time
  {
    return $this->dates['on_hold_date'] ?? $this->mutateDate($this->attributes['on_hold_date'] ?? null);
  }

  public function setOnHoldDate(mixed $onHoldDate): self
  {
    $this->attributes['on_hold_date'] = $onHoldDate;
    return $this;
  }

  public function getReResponseDate(): ?Time
  {
    return $this->dates['re_response_date'] ?? $this->mutateDate($this->attributes['re_response_date'] ?? null);
  }

  public function setReResponseDate(mixed $reResponseDate): self
  {
    $this->attributes['re_response_date'] = $reResponseDate;
    return $this;
  }

  public function getReCompletedDate(): ?Time
  {
    return $this->dates['re_completed_date'] ?? $this->mutateDate($this->attributes['re_completed_date'] ?? null);
  }

  public function setReCompletedDate(mixed $reCompletedDate): self
  {
    $this->attributes['re_completed_date'] = $reCompletedDate;
    return $this;
  }

  public function isEligibleForAutoClose(): bool
  {
    if ($this->getStatus() !== 'Resolved' || empty($this->attributes['completed_date'])) {
      return false;
    }

    $resolvedTimestamp = strtotime((string) $this->attributes['completed_date']);
    return (time() - $resolvedTimestamp) >= (24 * 3600);
  }
}