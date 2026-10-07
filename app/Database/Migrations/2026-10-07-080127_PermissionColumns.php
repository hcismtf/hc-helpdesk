<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PermissionColumns extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
      ],
      'code' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'unique' => true,
      ],
      'name' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
      ],
      'description' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true,
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
    $this->forge->createTable('permissions', true);
  }

  public function down()
  {
    $this->forge->dropTable('permissions', true);
  }
}
