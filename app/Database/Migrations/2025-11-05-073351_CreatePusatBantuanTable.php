<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePusatBantuanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'message' => [
                'type'          => 'VARCHAR',
                'constraint'    => '1000',
            ],
            'created_date' => [
                'type' => 'DATETIME',
            ]
        ]);

        $this->forge->createTable('pusban_message');
    }

    public function down()
    {
        $this->forge->dropTable('pusban_message');
    }
}