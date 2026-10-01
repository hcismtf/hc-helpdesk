<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorTicketTransactionsColumns extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_transactions')) {
            // 1. Tambahkan kolom reporter_id jika belum ada (dan salin isi dari emp_id jika ada)
            if (!$db->fieldExists('reporter_id', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `reporter_id` VARCHAR(36) NULL AFTER `id`");
                if ($db->fieldExists('emp_id', 'ticket_transactions')) {
                    $db->query("UPDATE `ticket_transactions` SET `reporter_id` = `emp_id` WHERE `reporter_id` IS NULL");
                }
            }

            // 2. Tambahkan kolom ticket_number jika belum ada
            if (!$db->fieldExists('ticket_number', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `ticket_number` VARCHAR(32) NULL AFTER `id`");
            }

            // 3. Pastikan created_by & modified_by adalah VARCHAR(100) untuk menampung nama user (bukan ID)
            if ($db->fieldExists('created_by', 'ticket_transactions')) {
                try {
                    $db->query("ALTER TABLE `ticket_transactions` MODIFY COLUMN `created_by` VARCHAR(100) NULL");
                } catch (\Throwable $e) {}
            }

            if ($db->fieldExists('modified_by', 'ticket_transactions')) {
                try {
                    $db->query("ALTER TABLE `ticket_transactions` MODIFY COLUMN `modified_by` VARCHAR(100) NULL");
                } catch (\Throwable $e) {}
            }

            // 4. Drop kolom deprecated yang tidak diperlukan (nip_encrypted, monitoring_url)
            if ($db->fieldExists('nip_encrypted', 'ticket_transactions')) {
                try {
                    $db->query("ALTER TABLE `ticket_transactions` DROP COLUMN `nip_encrypted`");
                } catch (\Throwable $e) {}
            }

            if ($db->fieldExists('monitoring_url', 'ticket_transactions')) {
                try {
                    $db->query("ALTER TABLE `ticket_transactions` DROP COLUMN `monitoring_url`");
                } catch (\Throwable $e) {}
            }

            // 5. Drop kolom emp_id setelah disalin ke reporter_id
            if ($db->fieldExists('emp_id', 'ticket_transactions')) {
                try {
                    if ($this->indexExists($db, 'ticket_transactions', 'idx_tt_emp_id')) {
                        $db->query("DROP INDEX `idx_tt_emp_id` ON `ticket_transactions`");
                    }
                    $db->query("ALTER TABLE `ticket_transactions` DROP COLUMN `emp_id`");
                } catch (\Throwable $e) {}
            }

            // 6. Indexing untuk pencarian cepat
            if (!$this->indexExists($db, 'ticket_transactions', 'idx_tt_reporter_id')) {
                try {
                    $db->query("CREATE INDEX `idx_tt_reporter_id` ON `ticket_transactions` (`reporter_id`)");
                } catch (\Throwable $e) {}
            }

            if (!$this->indexExists($db, 'ticket_transactions', 'idx_tt_created_by')) {
                try {
                    $db->query("CREATE INDEX `idx_tt_created_by` ON `ticket_transactions` (`created_by`)");
                } catch (\Throwable $e) {}
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_transactions')) {
            if (!$db->fieldExists('nip_encrypted', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `nip_encrypted` VARCHAR(255) NULL");
            }
            if (!$db->fieldExists('monitoring_url', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `monitoring_url` VARCHAR(255) NULL");
            }
        }
    }

    private function indexExists($db, string $table, string $indexName): bool
    {
        $query = $db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($query->getResultArray()) > 0;
    }
}
