<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTiketTrxIdToTicketIdInResponse extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_response')) {
            // Drop Foreign Key jika ada yang menempel di tiket_trx_id
            try {
                $fks = $db->query("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                      AND TABLE_NAME = 'ticket_response' 
                      AND COLUMN_NAME = 'tiket_trx_id' 
                      AND REFERENCED_TABLE_NAME IS NOT NULL
                ")->getResultArray();

                foreach ($fks as $fk) {
                    try {
                        $db->query("ALTER TABLE `ticket_response` DROP FOREIGN KEY `{$fk['CONSTRAINT_NAME']}`");
                    } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {}

            // Jika kolom tiket_trx_id masih ada dan ticket_id belum ada, ubah nama & tipe ke VARCHAR(36)
            if ($db->fieldExists('tiket_trx_id', 'ticket_response') && !$db->fieldExists('ticket_id', 'ticket_response')) {
                try {
                    $db->query("ALTER TABLE `ticket_response` CHANGE COLUMN `tiket_trx_id` `ticket_id` VARCHAR(36) NOT NULL");
                } catch (\Throwable $e) {
                    // Fallback
                    $db->query("ALTER TABLE `ticket_response` ADD COLUMN `ticket_id` VARCHAR(36) NULL AFTER `id`");
                    $db->query("UPDATE `ticket_response` SET `ticket_id` = `tiket_trx_id`");
                    $db->query("ALTER TABLE `ticket_response` DROP COLUMN `tiket_trx_id`");
                }
            } elseif ($db->fieldExists('tiket_trx_id', 'ticket_response') && $db->fieldExists('ticket_id', 'ticket_response')) {
                // Jika dua-duanya ada, salin nilai dan drop tiket_trx_id
                $db->query("UPDATE `ticket_response` SET `ticket_id` = `tiket_trx_id` WHERE `ticket_id` IS NULL");
                $db->query("ALTER TABLE `ticket_response` MODIFY COLUMN `ticket_id` VARCHAR(36) NOT NULL");
                $db->query("ALTER TABLE `ticket_response` DROP COLUMN `tiket_trx_id`");
            } elseif ($db->fieldExists('ticket_id', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` MODIFY COLUMN `ticket_id` VARCHAR(36) NOT NULL");
            }

            // Pastikan Index pada ticket_id terpasang
            try {
                $checkIndex = $db->query("SHOW INDEX FROM `ticket_response` WHERE Key_name = 'idx_tr_ticket_id'")->getResultArray();
                if (empty($checkIndex)) {
                    $db->query("CREATE INDEX `idx_tr_ticket_id` ON `ticket_response` (`ticket_id`)");
                }
            } catch (\Throwable $e) {}
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_response')) {
            if ($db->fieldExists('ticket_id', 'ticket_response') && !$db->fieldExists('tiket_trx_id', 'ticket_response')) {
                $db->query("ALTER TABLE `ticket_response` CHANGE COLUMN `ticket_id` `tiket_trx_id` VARCHAR(36) NOT NULL");
            }
        }
    }
}
