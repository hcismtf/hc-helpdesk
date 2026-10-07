<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RolePermissionColumn extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'role_id' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
      ],
      'permission_id' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
      ],
      'created_by' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
        'null' => true,
      ],
      'modified_by' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
        'null' => true,
      ],
      'created_date' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'modified_date' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);

    $this->forge->addPrimaryKey('id');
    $this->forge->createTable('role_permissions', true);
  }

  public function down()
  {
    $this->forge->dropTable('role_permissions', true);
  }
}
