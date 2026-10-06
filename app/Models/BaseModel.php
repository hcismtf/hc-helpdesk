<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\BaseEntity;
use App\Libraries\AuditorAware;

class BaseModel extends Model
{
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  protected $useAutoIncrement = false;
  protected $returnType = 'array';
  protected $protectFields = true;

  protected bool $useUuid = true;
  protected bool $useAudit = true;

  protected array $baseAllowedFields = [
    'id',
    'created_by',
    'created_date',
    'modified_by',
    'modified_date',
  ];

  protected $beforeInsert = ['prepareInsertData'];
  protected $beforeUpdate = ['prepareUpdateData'];

  private static array $tableFieldsCache = [];

  public function __construct($db = null, ?\CodeIgniter\Validation\ValidationInterface $validation = null)
  {
    parent::__construct($db, $validation);

    $this->allowedFields = array_values(array_unique(array_merge(
      $this->allowedFields ?? [],
      $this->baseAllowedFields
    )));
  }

  protected function prepareInsertData(array $data): array
  {
    if (is_object($data['data']) && $data['data'] instanceof BaseEntity) {
      $data['data']->prePersist();
      return $data;
    }

    if ($this->useUuid) {
      $pk = $this->primaryKey;
      if (empty($data['data'][$pk])) {
        $data['data'][$pk] = self::generateUuidV4();
      }
    }

    if ($this->useAudit) {
      $auditor = AuditorAware::getCurrentAuditor();
      $now = date('Y-m-d H:i:s');

      if ($this->tableHasField('created_by') && empty($data['data']['created_by'])) {
        $data['data']['created_by'] = $auditor;
      }

      if ($this->tableHasField('created_date') && empty($data['data']['created_date'])) {
        $data['data']['created_date'] = $now;
      } elseif ($this->tableHasField('created_at') && empty($data['data']['created_at'])) {
        $data['data']['created_at'] = $now;
      }

      if ($this->tableHasField('modified_by') && empty($data['data']['modified_by'])) {
        $data['data']['modified_by'] = $auditor;
      }

      if ($this->tableHasField('modified_date') && empty($data['data']['modified_date'])) {
        $data['data']['modified_date'] = $now;
      } elseif ($this->tableHasField('updated_at') && empty($data['data']['updated_at'])) {
        $data['data']['updated_at'] = $now;
      }
    }

    return $data;
  }

  protected function prepareUpdateData(array $data): array
  {
    if (is_object($data['data']) && $data['data'] instanceof BaseEntity) {
      $data['data']->preUpdate();
      return $data;
    }

    if ($this->useAudit) {
      $auditor = AuditorAware::getCurrentAuditor();
      $now = date('Y-m-d H:i:s');

      if ($this->tableHasField('modified_by')) {
        $data['data']['modified_by'] = $auditor;
      } elseif ($this->tableHasField('updated_by')) {
        $data['data']['updated_by'] = $auditor;
      }

      if ($this->tableHasField('modified_date')) {
        $data['data']['modified_date'] = $now;
      } elseif ($this->tableHasField('updated_at')) {
        $data['data']['updated_at'] = $now;
      }
    }

    return $data;
  }

  public static function generateUuidV4(): string
  {
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40); // version 4
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80); // variant RFC 4122
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
  }

  protected function tableHasField(string $field): bool
  {
    if (empty($this->table)) {
      return in_array($field, $this->allowedFields, true);
    }

    if (!isset(self::$tableFieldsCache[$this->table])) {
      try {
        if ($this->db && $this->db->tableExists($this->table)) {
          $fields = $this->db->getFieldNames($this->table);
          self::$tableFieldsCache[$this->table] = is_array($fields) && !empty($fields)
            ? $fields
            : ($this->allowedFields ?? []);
        } else {
          self::$tableFieldsCache[$this->table] = $this->allowedFields ?? [];
        }
      } catch (\Throwable $e) {
        self::$tableFieldsCache[$this->table] = $this->allowedFields ?? [];
      }
    }

    return in_array($field, self::$tableFieldsCache[$this->table], true);
  }
}
