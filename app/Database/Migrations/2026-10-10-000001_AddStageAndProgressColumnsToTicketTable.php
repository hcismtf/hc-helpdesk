<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStageAndProgressColumnsToTicketTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('ticket', [
            'current_stage' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'status',
                'comment'    => 'Label tahapan dinamis alur tiket (fleksibel string)',
            ],
            'stage_progress' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
                'default'    => 0,
                'after'      => 'current_stage',
                'comment'    => 'Angka persentase progress tahapan (0-100)',
            ],
            'status_note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'stage_progress',
                'comment'    => 'Preview teks abu-abu di bawah judul tiket',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('ticket', [
            'current_stage',
            'stage_progress',
            'status_note',
        ]);
    }
}
