<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropContactColumnsAndFixAssignedTo extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_transactions')) {
            // 1. Drop kolom kontak yang redundan (emp_name, email, wa_no)
            $columnsToDrop = ['emp_name', 'email', 'wa_no'];
            foreach ($columnsToDrop as $col) {
                if ($db->fieldExists($col, 'ticket_transactions')) {
                    try {
                        $db->query("ALTER TABLE `ticket_transactions` DROP COLUMN `{$col}`");
                    } catch (\Throwable $e) {}
                }
            }

            // 2. Ubah tipe kolom assigned_to menjadi VARCHAR(36) NULL untuk menampung UUID
            if ($db->fieldExists('assigned_to', 'ticket_transactions')) {
                try {
                    $db->query("ALTER TABLE `ticket_transactions` MODIFY COLUMN `assigned_to` VARCHAR(36) NULL");
                } catch (\Throwable $e) {}
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('ticket_transactions')) {
            if (!$db->fieldExists('emp_name', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `emp_name` VARCHAR(150) NULL");
            }
            if (!$db->fieldExists('email', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `email` VARCHAR(150) NULL");
            }
            if (!$db->fieldExists('wa_no', 'ticket_transactions')) {
                $db->query("ALTER TABLE `ticket_transactions` ADD COLUMN `wa_no` VARCHAR(20) NULL");
            }
        }
    }
}
