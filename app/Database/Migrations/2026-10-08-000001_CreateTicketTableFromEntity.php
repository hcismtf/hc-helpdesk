<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketTableFromEntity extends Migration
{
    public function up()
    {
        $this->forge->addField([
            // Primary Key & ID fields (UUID v4)
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'ticket_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'request_type_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'sla_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'open',
            ],

            // PIC assignment fields (UUID v4)
            'pic_helpdesk_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'pic_dev_ba_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],

            // Rejection / notes
            'rejection_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // SLA calculation metrics
            'total_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            'sla_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'sla_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            // SLA Due Dates
            'response_due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resolution_due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            // SLA Actual Action Dates
            'response_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'done_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'on_hold_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            're_response_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            're_completed_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            // BaseEntity Audit Trails
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

        // Primary key & Unique Index
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('ticket_no');

        // Indexes for performance
        $this->forge->addKey('request_type_id');
        $this->forge->addKey('sla_id');
        $this->forge->addKey('status');
        $this->forge->addKey('pic_helpdesk_id');
        $this->forge->addKey('pic_dev_ba_id');
        $this->forge->addKey('sla_status');
        $this->forge->addKey('created_date');
        $this->forge->addKey(['status', 'created_date']);

        $this->forge->createTable('ticket', true);
    }

    public function down()
    {
        $this->forge->dropTable('ticket', true);
    }
}
