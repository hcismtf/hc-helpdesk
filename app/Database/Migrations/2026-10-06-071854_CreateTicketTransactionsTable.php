<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // UUID / ULID
            ],
            'ticket_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'reporter_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'assigned_to' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'req_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ticket_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'open',
            ],
            'ticket_priority' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'medium',
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'first_response_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'finish_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'modified_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'modified_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary Key & Unik
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('ticket_number');

        // Index untuk optimasi pencarian & relasi
        $this->forge->addKey('reporter_id');
        $this->forge->addKey('assigned_to');
        $this->forge->addKey('ticket_status');

        // Composite Index untuk query SLA & getAverageTimes
        $this->forge->addKey(['ticket_priority', 'created_date']);

        $this->forge->createTable('ticket_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_transactions', true);
    }
}