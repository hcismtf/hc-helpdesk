<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptimizeTicketingTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Sinkronisasi kolom pada tabel `tiket_trx`
        if ($db->tableExists('tiket_trx')) {
            $fieldsToAdd = [];

            if (!$db->fieldExists('assigned_to', 'tiket_trx')) {
                $fieldsToAdd['assigned_to'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'finish_date'
                ];
            }

            if (!$db->fieldExists('monitoring_url', 'tiket_trx')) {
                $fieldsToAdd['monitoring_url'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'message'
                ];
            }

            if (!$db->fieldExists('first_response_at', 'tiket_trx')) {
                $fieldsToAdd['first_response_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }

            if (!$db->fieldExists('due_date', 'tiket_trx')) {
                $fieldsToAdd['due_date'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }

            if (!$db->fieldExists('finish_date', 'tiket_trx')) {
                $fieldsToAdd['finish_date'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }

            if (!empty($fieldsToAdd)) {
                $this->forge->addColumn('tiket_trx', $fieldsToAdd);
            }

            // Tambahkan indexes pada `tiket_trx`
            $indexes = [
                'idx_tiket_status'      => 'CREATE INDEX idx_tiket_status ON tiket_trx (ticket_status)',
                'idx_tiket_priority'    => 'CREATE INDEX idx_tiket_priority ON tiket_trx (ticket_priority)',
                'idx_tiket_assigned_to' => 'CREATE INDEX idx_tiket_assigned_to ON tiket_trx (assigned_to)',
                'idx_tiket_created_date' => 'CREATE INDEX idx_tiket_created_date ON tiket_trx (created_date)',
                'idx_tiket_emp_id'       => 'CREATE INDEX idx_tiket_emp_id ON tiket_trx (emp_id)'
            ];

            foreach ($indexes as $indexName => $sql) {
                if (!$this->indexExists($db, 'tiket_trx', $indexName)) {
                    $db->query($sql);
                }
            }
        }

        // 2. Sinkronisasi kolom & index pada tabel `tiket_transactions`
        if ($db->tableExists('tiket_transactions')) {
            $trxFieldsToAdd = [];

            if (!$db->fieldExists('priority', 'tiket_transactions')) {
                $trxFieldsToAdd['priority'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'status'
                ];
            }

            if (!$db->fieldExists('assigned_to', 'tiket_transactions')) {
                $trxFieldsToAdd['assigned_to'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'priority'
                ];
            }

            if (!empty($trxFieldsToAdd)) {
                $this->forge->addColumn('tiket_transactions', $trxFieldsToAdd);
            }

            $trxIndexes = [
                'idx_trx_tiket_id'   => 'CREATE INDEX idx_trx_tiket_id ON tiket_transactions (tiket_trx_id)',
                'idx_trx_created_at' => 'CREATE INDEX idx_trx_created_at ON tiket_transactions (created_at)'
            ];

            if ($db->fieldExists('user_id', 'tiket_transactions')) {
                $trxIndexes['idx_trx_user_id'] = 'CREATE INDEX idx_trx_user_id ON tiket_transactions (user_id)';
            }

            foreach ($trxIndexes as $indexName => $sql) {
                if (!$this->indexExists($db, 'tiket_transactions', $indexName)) {
                    $db->query($sql);
                }
            }
        }

        // 3. Optimasi & Indexing pada tabel `tiket_att`
        if ($db->tableExists('tiket_att')) {
            if (!$this->indexExists($db, 'tiket_att', 'idx_att_tiket_id')) {
                $db->query('CREATE INDEX idx_att_tiket_id ON tiket_att (tiket_trx_id)');
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('tiket_trx')) {
            $dropIndexes = ['idx_tiket_status', 'idx_tiket_priority', 'idx_tiket_assigned_to', 'idx_tiket_created_date', 'idx_tiket_emp_id'];
            foreach ($dropIndexes as $idx) {
                if ($this->indexExists($db, 'tiket_trx', $idx)) {
                    $db->query("DROP INDEX {$idx} ON tiket_trx");
                }
            }
        }

        if ($db->tableExists('tiket_transactions')) {
            $dropTrxIndexes = ['idx_trx_tiket_id', 'idx_trx_created_at', 'idx_trx_user_id'];
            foreach ($dropTrxIndexes as $idx) {
                if ($this->indexExists($db, 'tiket_transactions', $idx)) {
                    $db->query("DROP INDEX {$idx} ON tiket_transactions");
                }
            }
        }

        if ($db->tableExists('tiket_att')) {
            if ($this->indexExists($db, 'tiket_att', 'idx_att_tiket_id')) {
                $db->query("DROP INDEX idx_att_tiket_id ON tiket_att");
            }
        }
    }

    /**
     * Helper to check if an index already exists on a table
     */
    private function indexExists($db, string $table, string $indexName): bool
    {
        $query = $db->query("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($query->getResultArray()) > 0;
    }
}
