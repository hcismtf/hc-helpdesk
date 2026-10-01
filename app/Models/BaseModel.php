<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    // Hook CodeIgniter 4 sebelum insert dan update data
    protected $beforeInsert = ['generateUuid', 'fillAuditCreatedFields'];
    protected $beforeUpdate = ['fillAuditModifiedFields'];

    public function __construct($db = null, ?\CodeIgniter\Validation\ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        if (!in_array('generateUuid', $this->beforeInsert, true)) {
            array_unshift($this->beforeInsert, 'generateUuid');
        }
        if (!in_array('fillAuditCreatedFields', $this->beforeInsert, true)) {
            $this->beforeInsert[] = 'fillAuditCreatedFields';
        }
        if (!in_array('fillAuditModifiedFields', $this->beforeUpdate, true)) {
            $this->beforeUpdate[] = 'fillAuditModifiedFields';
        }
    }

    /**
     * Otomatis generate UUID v4 untuk primary key jika belum diisi
     *
     * @param array $data
     * @return array
     */
    protected function generateUuid(array $data): array
    {
        $primaryKey = $this->primaryKey;

        // Pastikan primaryKey terdaftar di allowedFields
        if (!in_array($primaryKey, $this->allowedFields, true)) {
            $this->allowedFields[] = $primaryKey;
        }

        // Generate UUID v4 jika belum ada nilai pada primaryKey
        if (!isset($data['data'][$primaryKey]) || empty($data['data'][$primaryKey])) {
            $bytes = random_bytes(16);
            $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40); // version 4
            $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80); // variant RFC 4122
            $data['data'][$primaryKey] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
        }

        return $data;
    }

    /**
     * Otomatis mengisi data audit saat create data baru (created_by dengan nama user, bukan ID)
     *
     * @param array $data
     * @return array
     */
    protected function fillAuditCreatedFields(array $data): array
    {
        $actorName = $this->getCurrentActorName();

        // created_by (nama user yang sedang login)
        if (in_array('created_by', $this->allowedFields, true)) {
            if (!isset($data['data']['created_by']) || empty($data['data']['created_by'])) {
                $data['data']['created_by'] = $actorName;
            }
        }

        // created_date atau created_at
        if (in_array('created_date', $this->allowedFields, true)) {
            if (!isset($data['data']['created_date']) || empty($data['data']['created_date'])) {
                $data['data']['created_date'] = date('Y-m-d H:i:s');
            }
        } elseif (in_array('created_at', $this->allowedFields, true)) {
            if (!isset($data['data']['created_at']) || empty($data['data']['created_at'])) {
                $data['data']['created_at'] = date('Y-m-d H:i:s');
            }
        }

        return $data;
    }

    /**
     * Otomatis mengisi data audit saat update data (modified_by dengan nama user, bukan ID)
     *
     * @param array $data
     * @return array
     */
    protected function fillAuditModifiedFields(array $data): array
    {
        $actorName = $this->getCurrentActorName();

        // modified_by / updated_by (nama user yang sedang login)
        if (in_array('modified_by', $this->allowedFields, true)) {
            if (!isset($data['data']['modified_by']) || empty($data['data']['modified_by'])) {
                $data['data']['modified_by'] = $actorName;
            }
        } elseif (in_array('updated_by', $this->allowedFields, true)) {
            if (!isset($data['data']['updated_by']) || empty($data['data']['updated_by'])) {
                $data['data']['updated_by'] = $actorName;
            }
        }

        // modified_date atau updated_at
        if (in_array('modified_date', $this->allowedFields, true)) {
            if (!isset($data['data']['modified_date']) || empty($data['data']['modified_date'])) {
                $data['data']['modified_date'] = date('Y-m-d H:i:s');
            }
        } elseif (in_array('updated_at', $this->allowedFields, true)) {
            if (!isset($data['data']['updated_at']) || empty($data['data']['updated_at'])) {
                $data['data']['updated_at'] = date('Y-m-d H:i:s');
            }
        }

        return $data;
    }

    /**
     * Dapatkan nama user/actor yang sedang login untuk keperluan audit (bukan ID)
     *
     * @return string
     */
    protected function getCurrentActorName(): string
    {
        try {
            if (function_exists('session')) {
                $session = session();
                if ($session) {
                    $username = $session->get('username') ?? $session->get('name');
                    if (!empty($username)) {
                        return (string)$username;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Context non-session (e.g. CLI/Migration/Job)
        }

        return 'system';
    }
}
