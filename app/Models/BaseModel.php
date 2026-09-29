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

    // Hook CodeIgniter 4 sebelum insert data
    protected $beforeInsert = ['generateUuid'];

    public function __construct($db = null, ?\CodeIgniter\Validation\ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        if (!in_array('generateUuid', $this->beforeInsert, true)) {
            array_unshift($this->beforeInsert, 'generateUuid');
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
}
