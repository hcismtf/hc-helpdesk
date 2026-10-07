<?php

namespace App\Entities;

use CodeIgniter\I18n\Time;

/**
 * Entity TicketAttachment
 * Merepresentasikan berkas lampiran tiket pada tabel `ticket_attachment`
 */
class TicketAttachmentEntity extends BaseEntity
{
  protected $casts = [
    'ticket_id'  => 'string',
    'file_name'  => 'string',
    'file_path'  => 'string',
    'file_size'  => '?integer',
    'file_type'  => '?string',
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

  public function getFileType(): ?string
  {
    return $this->attributes['file_type'] ?? null;
  }

  public function setFileType(?string $fileType): self
  {
    $this->attributes['file_type'] = $fileType;
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
