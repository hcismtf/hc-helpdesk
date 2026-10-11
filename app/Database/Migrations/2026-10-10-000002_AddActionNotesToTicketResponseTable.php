<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActionNotesToTicketResponseTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('ticket_response', [
            'action_notes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'after'   => 'reply',
                'comment' => 'Catatan aksi / instruksi konfirmasi butuh respon dari user',
            ],
            'reply_notes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'after'   => 'action_notes',
                'comment' => 'Catatan balasan tambahan / alias note',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('ticket_response', [
            'action_notes',
            'reply_notes',
        ]);
    }
}
