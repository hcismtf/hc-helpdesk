<?php

namespace App\Entities;

use CodeIgniter\I18n\Time;

/**
 * Entity TicketAttachment
 * Merepresentasikan berkas lampiran tiket pada tabel `ticket_attachment` / `ticket_attachments`
 * Mewarisi 'id' (UUID), 'created_by', 'created_date', 'modified_by', 'modified_date' dari BaseEntity.
 */
class TicketAttachmentEntity extends BaseEntity
{
  protected $casts = [
    'ticket_id'   => 'string',
    'response_id' => '?string',
    'file_name'   => 'string',
    'file_path'   => 'string',
    'file_size'   => '?integer',
    'mime_type'   => '?string',
    'file_type'   => '?string',
  ];

  // --- Getters & Setters ---

  public function getTicketId(): ?string
  {
    return $this->attributes['ticket_id'] ?? $this->attributes['tiket_trx_id'] ?? null;
  }

  public function setTicketId(string $ticketId): self
  {
    $this->attributes['ticket_id'] = $ticketId;
    return $this;
  }

  public function getResponseId(): ?string
  {
    return $this->attributes['response_id'] ?? $this->attributes['comment_id'] ?? null;
  }

  public function setResponseId(?string $responseId): self
  {
    $this->attributes['response_id'] = $responseId;
    return $this;
  }

  public function getCommentId(): ?string
  {
    return $this->getResponseId();
  }

  public function setCommentId(?string $commentId): self
  {
    return $this->setResponseId($commentId);
  }

  public function getFileName(): ?string
  {
    return $this->attributes['file_name'] ?? null;
  }

  public function setFileName(string $fileName): self
  {
    $this->attributes['file_name'] = $fileName;
    return $this;
  }

  public function getFilePath(): ?string
  {
    return $this->attributes['file_path'] ?? null;
  }

  public function setFilePath(string $filePath): self
  {
    $this->attributes['file_path'] = $filePath;
    return $this;
  }

  public function getFileSize(): ?int
  {
    return isset($this->attributes['file_size']) ? (int) $this->attributes['file_size'] : null;
  }

  public function setFileSize(?int $fileSize): self
  {
    $this->attributes['file_size'] = $fileSize;
    return $this;
  }

  public function getMimeType(): ?string
  {
    return $this->attributes['mime_type'] ?? $this->attributes['file_type'] ?? null;
  }

  public function setMimeType(?string $mimeType): self
  {
    $this->attributes['mime_type'] = $mimeType;
    $this->attributes['file_type'] = $mimeType;
    return $this;
  }

  public function getFileType(): ?string
  {
    return $this->getMimeType();
  }

  public function setFileType(?string $fileType): self
  {
    return $this->setMimeType($fileType);
  }

  public function getCreatedAt(): mixed
  {
    return $this->attributes['created_at'] ?? $this->getCreatedDate();
  }

  public function setCreatedAt(mixed $createdAt): self
  {
    $this->attributes['created_at'] = $createdAt;
    $this->setCreatedDate($createdAt);
    return $this;
  }

  /**
   * Helper dekripsi nama file jika tersimpan dalam bentuk terenkripsi
   */
  public function getDecryptedFileName(): string
  {
    $rawName = $this->getFileName();
    if (empty($rawName)) {
      return '';
    }

    try {
      $encrypter = \Config\Services::encrypter();
      // Periksa apakah berupa hex yang valid untuk didekripsi
      if (ctype_xdigit($rawName) && strlen($rawName) % 2 === 0) {
        $decrypted = $encrypter->decrypt(hex2bin($rawName));
        if ($decrypted !== false) {
          return (string) $decrypted;
        }
      }
    } catch (\Throwable $e) {
      // Jika bukan terenkripsi atau gagal dekripsi, kembalikan teks aslinya
    }

    return (string) $rawName;
  }
}
