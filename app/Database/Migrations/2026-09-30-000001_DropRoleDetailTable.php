<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropRoleDetailTable extends Migration
{
    public function up()
    {
        // 1. Sync data role_id ke tabel users terlebih dahulu jika ada yang belum terisi
        $db = \Config\Database::connect();
        
        if ($db->tableExists('role_detail') && $db->tableExists('users')) {
            $db->query("
                UPDATE users u
                INNER JOIN role_detail rd ON rd.user_id = u.id
                SET u.role_id = rd.role_id
                WHERE u.role_id IS NULL OR u.role_id = ''
            ");

            // 2. Drop table role_detail
            $this->forge->dropTable('role_detail', true);
        }
    }

    public function down()
    {
        // Recreate role_detail table jika rollback diperlukan
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'role_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'modified_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'modified_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('role_detail', true);
    }
}
