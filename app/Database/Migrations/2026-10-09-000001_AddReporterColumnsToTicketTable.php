<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReporterColumnsToTicketTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('ticket', [
            'reporter_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'status',
            ],
            'reporter_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'reporter_id',
            ],
            'reporter_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'reporter_email',
            ],
        ]);

        $db = \Config\Database::connect();
        try {
            $db->query("CREATE INDEX `idx_ticket_reporter_id` ON `ticket` (`reporter_id`)");
        } catch (\Throwable $e) {}

        try {
            $db->query("CREATE INDEX `idx_ticket_reporter_email` ON `ticket` (`reporter_email`)");
        } catch (\Throwable $e) {}
    }

    public function down()
    {
        $this->forge->dropColumn('ticket', [
            'reporter_id',
            'reporter_email',
            'reporter_phone',
        ]);
    }
}
