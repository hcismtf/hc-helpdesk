<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorTicketTablesNaming extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Rename existing reply table `tiket_transactions` to `ticket_response`
        if ($db->tableExists('tiket_transactions') && !$db->tableExists('ticket_response')) {
            $db->query("RENAME TABLE tiket_transactions TO ticket_response");
        }

        // Sinkronisasi kolom `ticket_id` di `ticket_response`
        if ($db->tableExists('ticket_response')) {
            if ($db->fieldExists('tiket_trx_id', 'ticket_response') && !$db->fieldExists('ticket_id', 'ticket_response')) {
                $db->query("ALTER TABLE ticket_response CHANGE COLUMN tiket_trx_id ticket_id VARCHAR(50) NOT NULL");
            }
        }

        // 2. Rename existing core ticket table `tiket_trx` to `ticket_transactions`
        if ($db->tableExists('tiket_trx') && !$db->tableExists('ticket_transactions')) {
            $db->query("RENAME TABLE tiket_trx TO ticket_transactions");
        }

        // 3. Rename existing attachment table `tiket_att` to `ticket_attachment`
        if ($db->tableExists('tiket_att') && !$db->tableExists('ticket_attachment')) {
            $db->query("RENAME TABLE tiket_att TO ticket_attachment");
        }

        // Sinkronisasi kolom `ticket_id` di `ticket_attachment`
        if ($db->tableExists('ticket_attachment')) {
            if ($db->fieldExists('tiket_trx_id', 'ticket_attachment') && !$db->fieldExists('ticket_id', 'ticket_attachment')) {
                $db->query("ALTER TABLE ticket_attachment CHANGE COLUMN tiket_trx_id ticket_id VARCHAR(50) NOT NULL");
            }
        }

        // 4. Tambahkan Index untuk Performa Cepat
        if ($db->tableExists('ticket_transactions')) {
            $ttIndexes = [
                'idx_tt_status'       => 'CREATE INDEX idx_tt_status ON ticket_transactions (ticket_status)',
                'idx_tt_priority'     => 'CREATE INDEX idx_tt_priority ON ticket_transactions (ticket_priority)',
                'idx_tt_assigned_to'  => 'CREATE INDEX idx_tt_assigned_to ON ticket_transactions (assigned_to)',
                'idx_tt_created_date' => 'CREATE INDEX idx_tt_created_date ON ticket_transactions (created_date)',
                'idx_tt_emp_id'       => 'CREATE INDEX idx_tt_emp_id ON ticket_transactions (emp_id)'
            ];
            foreach ($ttIndexes as $idx => $sql) {
                if (!$this->indexExists($db, 'ticket_transactions', $idx)) {
                    $db->query($sql);
                }
            }
        }

        if ($db->tableExists('ticket_response')) {
            $trIndexes = [
                'idx_tr_ticket_id'  => 'CREATE INDEX idx_tr_ticket_id ON ticket_response (ticket_id)',
                'idx_tr_created_at' => 'CREATE INDEX idx_tr_created_at ON ticket_response (created_at)'
            ];
            foreach ($trIndexes as $idx => $sql) {
                if (!$this->indexExists($db, 'ticket_response', $idx)) {
                    $db->query($sql);
                }
            }
        }

        if ($db->tableExists('ticket_attachment')) {
            if (!$this->indexExists($db, 'ticket_attachment', 'idx_ta_ticket_id')) {
                $db->query('CREATE INDEX idx_ta_ticket_id ON ticket_attachment (ticket_id)');
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_attachment') && !$db->tableExists('tiket_att')) {
            if ($db->fieldExists('ticket_id', 'ticket_attachment') && !$db->fieldExists('tiket_trx_id', 'ticket_attachment')) {
                $db->query("ALTER TABLE ticket_attachment CHANGE COLUMN ticket_id tiket_trx_id VARCHAR(50) NOT NULL");
            }
            $db->query("RENAME TABLE ticket_attachment TO tiket_att");
        }

        if ($db->tableExists('ticket_transactions') && !$db->tableExists('tiket_trx')) {
            $db->query("RENAME TABLE ticket_transactions TO tiket_trx");
        }

        if ($db->tableExists('ticket_response') && !$db->tableExists('tiket_transactions')) {
            if ($db->fieldExists('ticket_id', 'ticket_response') && !$db->fieldExists('tiket_trx_id', 'ticket_response')) {
                $db->query("ALTER TABLE ticket_response CHANGE COLUMN ticket_id tiket_trx_id VARCHAR(50) NOT NULL");
            }
            $db->query("RENAME TABLE ticket_response TO tiket_transactions");
        }
    }

    private function indexExists($db, string $table, string $indexName): bool
    {
        $query = $db->query("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($query->getResultArray()) > 0;
    }
}
