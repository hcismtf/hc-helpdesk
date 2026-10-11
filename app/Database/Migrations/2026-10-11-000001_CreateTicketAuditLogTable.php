<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketAuditLogTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // UUID v4
            ],
            'ticket_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // Relasi ke ticket.id
            ],
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36, // Relasi ke users.id
                'null'       => true,
            ],
            'actor_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100, // Misal: change_status, update_properties, assign_specialist
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100, // Misal: status, priority, assigned_to
                'null'       => true,
            ],
            'old_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reason_notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('ticket_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('created_date');
        $this->forge->addKey(['ticket_id', 'created_date']);

        $this->forge->createTable('ticket_audit_log', true);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_audit_log', true);
    }
}
