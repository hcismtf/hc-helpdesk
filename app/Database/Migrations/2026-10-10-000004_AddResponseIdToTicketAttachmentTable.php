<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResponseIdToTicketAttachmentTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_attachment')) {
            if (!$db->fieldExists('response_id', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'response_id' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 36,
                        'null'       => true,
                        'after'      => 'ticket_id',
                        'comment'    => 'FK ke ticket_response.id (UUID v4) jika diunggah saat membalas',
                    ],
                ]);
            }

            try {
                $db->query("CREATE INDEX `idx_ta_response_id` ON `ticket_attachment` (`response_id`)");
            } catch (\Throwable $e) {}

            // Perbarui view ticket_attachments
            try {
                $db->query("CREATE OR REPLACE VIEW `ticket_attachments` AS SELECT * FROM `ticket_attachment`");
            } catch (\Throwable $e) {}
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('ticket_attachment') && $db->fieldExists('response_id', 'ticket_attachment')) {
            $this->forge->dropColumn('ticket_attachment', ['response_id']);
        }
    }
}
