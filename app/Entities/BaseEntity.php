<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class BaseEntity extends Entity
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        // Auto generate UUID v4 jika ID belum diset saat inisiasi Entity
        if (empty($this->attributes['id'])) {
            $bytes = random_bytes(16);
            $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
            $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
            $this->attributes['id'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
        }
    }
}
