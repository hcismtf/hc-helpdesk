<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RolePermissionColumn extends Migration
{
  public function up()
  {
    $this->forge->addColumn('role_permissions', [
      'created_by' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
        'null' => true,
        'after' => 'permission_id',
      ],
      'modified_by' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
        'null' => true,
        'after' => 'created_by',
      ],
      'created_date' => [
        'type' => 'DATETIME',
        'null' => true,
        'after' => 'modified_by',
      ],
      'modified_date' => [
        'type' => 'DATETIME',
        'null' => true,
        'after' => 'created_date',
      ],
    ]);
  }

  public function down()
  {
    $this->forge->dropColumn('role_permissions', [
      'created_by',
      'modified_by',
      'created_date',
      'modified_date',
    ]);
  }
}
