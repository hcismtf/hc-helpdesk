<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketResponseTableFromEntity extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Backup data lama jika ada tabel ticket_response lama yang masih berformat int
        if ($db->tableExists('ticket_response') && !$db->tableExists('ticket_response_legacy')) {
            try {
                $db->query("RENAME TABLE `ticket_response` TO `ticket_response_legacy`");
            } catch (\Throwable $e) {}
        }

        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // UUID v4
            ],
            'ticket_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // Relasi ke ticket.id (UUID v4)
            ],
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // Relasi ke users.id (UUID v4)
                'null'       => true,
            ],
            'author_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'reply' => [
                'type' => 'TEXT',
            ],
            'is_internal' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'priority' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'assigned_to' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // Relasi ke users.id (UUID v4)
                'null'       => true,
            ],

            // BaseEntity audit columns
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

        // Indexes untuk performa pencarian timeline cepat
        $this->forge->addKey('ticket_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('assigned_to');
        $this->forge->addKey('created_date');
        $this->forge->addKey(['ticket_id', 'created_date']);

        $this->forge->createTable('ticket_response', true);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_response', true);

        $db = \Config\Database::connect();
        if ($db->tableExists('ticket_response_legacy')) {
            try {
                $db->query("RENAME TABLE `ticket_response_legacy` TO `ticket_response`");
            } catch (\Throwable $e) {}
        }
    }
}
