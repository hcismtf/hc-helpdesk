<?php

namespace App\Entities;

use CodeIgniter\I18n\Time;
use App\Entities\TicketStatus;

class TicketEntity extends BaseEntity
{
  protected $dates = [
    'response_due_date',
    'resolution_due_date',
  ];

  protected $casts = [
    'ticket_no' => 'string',
    'request_type_id' => 'string',
    'sla_id' => 'string',
    'title' => 'string',
    'description' => 'string',
    'status' => 'string',

    'reporter_id' => '?string',
    'reporter_email' => '?string',
    'reporter_phone' => '?string',

    'pic_helpdesk_id' => '?string',
    'pic_dev_ba_id' => '?string',

    'rejection_notes' => '?string',

    'total_hours' => '?float',
    'sla_status' => '?string',
    'sla_percentage' => '?float',
  ];

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

  public function getReporterId(): ?string
  {
    return $this->attributes['reporter_id'] ?? null;
  }

  public function setReporterId(?string $reporterId): self
  {
    $this->attributes['reporter_id'] = $reporterId;
    return $this;
  }

  public function getReporterEmail(): ?string
  {
    return $this->attributes['reporter_email'] ?? null;
  }

  public function setReporterEmail(?string $reporterEmail): self
  {
    $this->attributes['reporter_email'] = $reporterEmail;
    return $this;
  }

  public function getReporterPhone(): ?string
  {
    return $this->attributes['reporter_phone'] ?? null;
  }

  public function setReporterPhone(?string $reporterPhone): self
  {
    $this->attributes['reporter_phone'] = $reporterPhone;
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
    return isset($this->attributes['resolution_due_date'])
      ? $this->mutateDate($this->attributes['resolution_due_date'])
      : null;
  }

  public function setResolutionDueDate(mixed $resolutionDueDate): self
  {
    $this->attributes['resolution_due_date'] = $resolutionDueDate;
    return $this;
  }

  public function isReopened(): bool
  {
    return TicketStatus::normalize($this->getStatus()) === TicketStatus::REOPENED;
  }

  public function isClosed(): bool
  {
    return TicketStatus::isClosedOrResolved($this->getStatus());
  }

  public function getStatusLabel(): string
  {
    return TicketStatus::getLabel($this->getStatus());
  }

  public function getStatusBadgeClass(): string
  {
    return TicketStatus::getStatusBadgeClass($this->getStatus());
  }
}