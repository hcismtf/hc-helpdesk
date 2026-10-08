<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveLegacyStatusDatesFromTicketTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket')) {
            $legacyColumns = [
                'response_date',
                'completed_date',
                'done_date',
                'on_hold_date',
                're_response_date',
                're_completed_date',
            ];

            $columnsToDrop = [];
            foreach ($legacyColumns as $col) {
                if ($db->fieldExists($col, 'ticket')) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $this->forge->dropColumn('ticket', $columnsToDrop);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket')) {
            $fields = [
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
            ];

            $this->forge->addColumn('ticket', $fields);
        }
    }
}
