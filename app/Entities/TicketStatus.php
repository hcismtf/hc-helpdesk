<?php

namespace App\Entities;

class TicketStatus extends BaseEntity
{
    public const OPEN        = 'open';
    public const IN_PROGRESS = 'in_progress';
    public const ON_HOLD     = 'on_hold';
    public const RESOLVED    = 'resolved';
    public const DONE        = 'done';
    public const CLOSED      = 'closed';
    public const REOPENED    = 'reopened';
    public const REJECTED    = 'rejected';
    public const CANCELLED   = 'cancelled';

    protected $casts = [
        'code'          => 'string',
        'name'          => 'string',
        'description'   => '?string',
        'badge_color'   => '?string',
        'badge_class'   => '?string',
        'is_final'      => 'boolean',
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public static function getDefinitions(): array
    {
        return [
            self::OPEN => [
                'code'          => self::OPEN,
                'name'          => 'Open',
                'description'   => 'Tiket baru dibuat dan menunggu penanganan pertama',
                'badge_color'   => '#2563EB',
                'badge_class'   => 'badge-primary',
                'is_final'      => false,
                'is_active'     => true,
                'display_order' => 1,
            ],
            self::IN_PROGRESS => [
                'code'          => self::IN_PROGRESS,
                'name'          => 'In Progress',
                'description'   => 'Tiket sedang dalam proses penanganan oleh PIC/tim support',
                'badge_color'   => '#D97706',
                'badge_class'   => 'badge-warning',
                'is_final'      => false,
                'is_active'     => true,
                'display_order' => 2,
            ],
            self::ON_HOLD => [
                'code'          => self::ON_HOLD,
                'name'          => 'On Hold',
                'description'   => 'Penanganan tiket ditangguhkan sementara (menunggu info/dependensi)',
                'badge_color'   => '#EAB308',
                'badge_class'   => 'badge-secondary',
                'is_final'      => false,
                'is_active'     => true,
                'display_order' => 3,
            ],
            self::RESOLVED => [
                'code'          => self::RESOLVED,
                'name'          => 'Resolved',
                'description'   => 'Solusi telah diberikan kepada pelapor, menunggu konfirmasi',
                'badge_color'   => '#0D9488',
                'badge_class'   => 'badge-info',
                'is_final'      => false,
                'is_active'     => true,
                'display_order' => 4,
            ],
            self::DONE => [
                'code'          => self::DONE,
                'name'          => 'Done',
                'description'   => 'Pekerjaan selesai dan dikonfirmasi',
                'badge_color'   => '#16A34A',
                'badge_class'   => 'badge-success',
                'is_final'      => true,
                'is_active'     => true,
                'display_order' => 5,
            ],
            self::CLOSED => [
                'code'          => self::CLOSED,
                'name'          => 'Closed',
                'description'   => 'Tiket telah ditutup secara permanen',
                'badge_color'   => '#475569',
                'badge_class'   => 'badge-dark',
                'is_final'      => true,
                'is_active'     => true,
                'display_order' => 6,
            ],
            self::REOPENED => [
                'code'          => self::REOPENED,
                'name'          => 'Reopened',
                'description'   => 'Tiket yang sebelumnya selesai/ditutup dibuka kembali karena kendala belum tuntas',
                'badge_color'   => '#DC2626',
                'badge_class'   => 'badge-danger',
                'is_final'      => false,
                'is_active'     => true,
                'display_order' => 7,
            ],
            self::REJECTED => [
                'code'          => self::REJECTED,
                'name'          => 'Rejected',
                'description'   => 'Pengajuan tiket ditolak oleh admin/helpdesk',
                'badge_color'   => '#991B1B',
                'badge_class'   => 'badge-danger',
                'is_final'      => true,
                'is_active'     => true,
                'display_order' => 8,
            ],
            self::CANCELLED => [
                'code'          => self::CANCELLED,
                'name'          => 'Cancelled',
                'description'   => 'Tiket dibatalkan oleh pelapor',
                'badge_color'   => '#64748B',
                'badge_class'   => 'badge-muted',
                'is_final'      => true,
                'is_active'     => true,
                'display_order' => 9,
            ],
        ];
    }

    public static function getAll(): array
    {
        $entities = [];
        foreach (self::getDefinitions() as $data) {
            $entities[] = new self($data);
        }
        return $entities;
    }

    public static function getCodes(): array
    {
        return array_keys(self::getDefinitions());
    }

    public static function isValid(?string $code): bool
    {
        if ($code === null || $code === '') {
            return false;
        }
        return array_key_exists(self::normalize($code), self::getDefinitions());
    }

    public static function normalize(?string $status): string
    {
        if ($status === null || trim($status) === '') {
            return self::OPEN;
        }

        $cleaned = strtolower(trim(str_replace(['-', ' '], '_', $status)));

        $synonyms = [
            'reopen'        => self::REOPENED,
            'buka_kembali'  => self::REOPENED,
            'open_again'    => self::REOPENED,
            'in_prog'       => self::IN_PROGRESS,
            'progress'      => self::IN_PROGRESS,
            'hold'          => self::ON_HOLD,
            'pending'       => self::ON_HOLD,
            'finish'        => self::DONE,
            'complete'      => self::DONE,
            'completed'     => self::DONE,
            'close'         => self::CLOSED,
            'resolve'       => self::RESOLVED,
            'reject'        => self::REJECTED,
            'cancel'        => self::CANCELLED,
        ];

        return $synonyms[$cleaned] ?? $cleaned;
    }

    public static function isReopenStatus(?string $status): bool
    {
        $normalized = self::normalize($status);
        return in_array($normalized, [self::REOPENED, 'reopen', 'buka_kembali'], true);
    }

    public static function isClosedOrResolved(?string $status): bool
    {
        $normalized = self::normalize($status);
        return in_array($normalized, [self::RESOLVED, self::DONE, self::CLOSED], true);
    }

    public static function getLabel(?string $code): string
    {
        $normalized = self::normalize($code);
        $defs = self::getDefinitions();
        return $defs[$normalized]['name'] ?? ucwords(str_replace('_', ' ', (string) $code));
    }

    public static function getStatusBadgeClass(?string $code): string
    {
        $normalized = self::normalize($code);
        $defs = self::getDefinitions();
        return $defs[$normalized]['badge_class'] ?? 'badge-secondary';
    }

    public function getCode(): ?string
    {
        return $this->attributes['code'] ?? null;
    }

    public function setCode(string $code): self
    {
        $this->attributes['code'] = self::normalize($code);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    public function setName(string $name): self
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->attributes['description'] ?? null;
    }

    public function setDescription(?string $description): self
    {
        $this->attributes['description'] = $description;
        return $this;
    }

    public function getBadgeColor(): ?string
    {
        return $this->attributes['badge_color'] ?? null;
    }

    public function setBadgeColor(?string $badgeColor): self
    {
        $this->attributes['badge_color'] = $badgeColor;
        return $this;
    }

    public function getBadgeClass(): ?string
    {
        return $this->attributes['badge_class'] ?? null;
    }

    public function setBadgeClass(?string $badgeClass): self
    {
        $this->attributes['badge_class'] = $badgeClass;
        return $this;
    }

    public function isFinal(): bool
    {
        return (bool) ($this->attributes['is_final'] ?? false);
    }

    public function setIsFinal(bool $isFinal): self
    {
        $this->attributes['is_final'] = $isFinal;
        return $this;
    }

    public function isActive(): bool
    {
        return (bool) ($this->attributes['is_active'] ?? true);
    }

    public function setIsActive(bool $isActive): self
    {
        $this->attributes['is_active'] = $isActive;
        return $this;
    }
}

class_alias(TicketStatus::class, TicketStatusEntity::class);
