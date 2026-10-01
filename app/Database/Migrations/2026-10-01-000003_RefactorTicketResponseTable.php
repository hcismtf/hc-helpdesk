<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorTicketResponseTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_response')) {
            // 1. Pastikan kolom id bertipe VARCHAR(36) untuk UUID v4
            if ($db->fieldExists('id', 'ticket_response')) {
                try {
                    $db->query("ALTER TABLE `ticket_response` MODIFY COLUMN `id` VARCHAR(36) NOT NULL");
                } catch (\Throwable $e) {}
            }

            // 2. Pastikan ticket_id bertipe VARCHAR(36)
            if ($db->fieldExists('ticket_id', 'ticket_response')) {
                try {
                    $db->query("ALTER TABLE `ticket_response` MODIFY COLUMN `ticket_id` VARCHAR(36) NOT NULL");
                } catch (\Throwable $e) {}
            }

            // 3. Pastikan user_id bertipe VARCHAR(36) NULL
            if (!$db->fieldExists('user_id', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` ADD COLUMN `user_id` VARCHAR(36) NULL AFTER `ticket_id`");
            } else {
                try {
                    $db->query("ALTER TABLE `ticket_response` MODIFY COLUMN `user_id` VARCHAR(36) NULL");
                } catch (\Throwable $e) {}
            }

            // 4. Tambahkan author_name VARCHAR(100) untuk audit string nama pengirim
            if (!$db->fieldExists('author_name', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` ADD COLUMN `author_name` VARCHAR(100) NULL AFTER `user_id`");
                
                // Salin dari tabel users jika user_id tersedia
                try {
                    $db->query("UPDATE `ticket_response` tr 
                                INNER JOIN `users` u ON tr.user_id = u.id 
                                SET tr.author_name = u.name 
                                WHERE tr.author_name IS NULL");
                } catch (\Throwable $e) {}
            }

            // 5. Tambahkan is_internal TINYINT(1) untuk internal note
            if (!$db->fieldExists('is_internal', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` ADD COLUMN `is_internal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `reply`");
            }

            // 6. Pastikan assigned_to bertipe VARCHAR(36) NULL
            if ($db->fieldExists('assigned_to', 'ticket_response')) {
                try {
                    $db->query("ALTER TABLE `ticket_response` MODIFY COLUMN `assigned_to` VARCHAR(36) NULL");
                } catch (\Throwable $e) {}
            }

            // 7. Drop kolom redundan submitted_by jika ada
            if ($db->fieldExists('submitted_by', 'ticket_response')) {
                try {
                    $db->query("ALTER TABLE `ticket_response` DROP COLUMN `submitted_by`");
                } catch (\Throwable $e) {}
            }

            // 8. Indexes untuk pencarian timeline dan histori cepat
            if (!$this->indexExists($db, 'ticket_response', 'idx_tr_ticket_id')) {
                try {
                    $db->query("CREATE INDEX `idx_tr_ticket_id` ON `ticket_response` (`ticket_id`)");
                } catch (\Throwable $e) {}
            }

            if (!$this->indexExists($db, 'ticket_response', 'idx_tr_user_id')) {
                try {
                    $db->query("CREATE INDEX `idx_tr_user_id` ON `ticket_response` (`user_id`)");
                } catch (\Throwable $e) {}
            }

            if (!$this->indexExists($db, 'ticket_response', 'idx_tr_created_at')) {
                try {
                    $db->query("CREATE INDEX `idx_tr_created_at` ON `ticket_response` (`created_at`)");
                } catch (\Throwable $e) {}
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_response')) {
            if ($db->fieldExists('is_internal', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` DROP COLUMN `is_internal`");
            }
            if ($db->fieldExists('author_name', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` DROP COLUMN `author_name`");
            }
            if (!$db->fieldExists('submitted_by', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` ADD COLUMN `submitted_by` VARCHAR(36) NULL");
            }
        }
    }

    private function indexExists($db, string $table, string $indexName): bool
    {
        $query = $db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($query->getResultArray()) > 0;
    }
}
