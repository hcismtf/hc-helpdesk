<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertAllPrimaryKeysToUuid extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Drop semua Foreign Key Constraints yang ada di database agar alter column tidak terblokir
        try {
            $fks = $db->query("
                SELECT TABLE_NAME, CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ")->getResultArray();

            foreach ($fks as $fk) {
                try {
                    $db->query("ALTER TABLE `{$fk['TABLE_NAME']}` DROP FOREIGN KEY `{$fk['CONSTRAINT_NAME']}`");
                } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {}

        // 2. Ubah tipe kolom foreign keys ke VARCHAR(36)
        $foreignColumns = [
            'ticket_transactions' => ['emp_id'],
            'ticket_response'     => ['ticket_id'],
            'ticket_attachment'   => ['ticket_id'],
            'users'               => ['role_id'],
            'role_permissions'    => ['role_id', 'permission_id']
        ];

        foreach ($foreignColumns as $tbl => $cols) {
            if ($db->tableExists($tbl)) {
                foreach ($cols as $col) {
                    if ($db->fieldExists($col, $tbl)) {
                        $isNullable = ($col === 'role_id') ? 'NULL' : 'NOT NULL';
                        try {
                            $db->query("ALTER TABLE `{$tbl}` MODIFY COLUMN `{$col}` VARCHAR(36) {$isNullable}");
                        } catch (\Throwable $e) {}
                    }
                }
            }
        }

        // 3. Daftar seluruh tabel master & transaksi yang akan dikonversi ke UUID v4
        $tables = [
            'role',
            'permissions',
            'role_permissions',
            'users',
            'request_type',
            'faq_detail',
            'sla_configuration',
            'report_jobs',
            'ticket_transactions',
            'ticket_response',
            'ticket_attachment'
        ];

        foreach ($tables as $tbl) {
            if (!$db->tableExists($tbl)) {
                continue;
            }

            // Tambahkan kolom temporary `new_uuid`
            if (!$db->fieldExists('new_uuid', $tbl)) {
                $db->query("ALTER TABLE `{$tbl}` ADD COLUMN `new_uuid` VARCHAR(36) NULL");
            }

            // Isi `new_uuid` untuk setiap baris
            $rows = $db->query("SELECT * FROM `{$tbl}`")->getResultArray();
            foreach ($rows as $row) {
                $currentId = (string)($row['id'] ?? '');
                // Jika id sudah berformat UUID v4, pertahankan; jika integer/bukan UUID, buat UUID baru
                if (strlen($currentId) === 36 && substr_count($currentId, '-') === 4) {
                    $uuidVal = $currentId;
                } else {
                    $uuidVal = $this->generateUuidV4();
                }

                if (isset($row['id'])) {
                    $db->query("UPDATE `{$tbl}` SET `new_uuid` = ? WHERE `id` = ?", [$uuidVal, $row['id']]);
                }
            }

            // Update relasi foreign keys sebelum kolom id diganti
            if ($tbl === 'role') {
                if ($db->tableExists('users') && $db->fieldExists('role_id', 'users')) {
                    $db->query("UPDATE `users` u INNER JOIN `role` r ON u.role_id = r.id SET u.role_id = r.new_uuid WHERE r.new_uuid IS NOT NULL");
                }
                if ($db->tableExists('role_permissions') && $db->fieldExists('role_id', 'role_permissions')) {
                    $db->query("UPDATE `role_permissions` rp INNER JOIN `role` r ON rp.role_id = r.id SET rp.role_id = r.new_uuid WHERE r.new_uuid IS NOT NULL");
                }
            }

            if ($tbl === 'permissions') {
                if ($db->tableExists('role_permissions') && $db->fieldExists('permission_id', 'role_permissions')) {
                    $db->query("UPDATE `role_permissions` rp INNER JOIN `permissions` p ON rp.permission_id = p.id SET rp.permission_id = p.new_uuid WHERE p.new_uuid IS NOT NULL");
                }
            }

            if ($tbl === 'ticket_transactions') {
                if ($db->tableExists('ticket_response') && $db->fieldExists('ticket_id', 'ticket_response')) {
                    $db->query("UPDATE `ticket_response` tr INNER JOIN `ticket_transactions` tt ON tr.ticket_id = tt.id SET tr.ticket_id = tt.new_uuid WHERE tt.new_uuid IS NOT NULL");
                }
                if ($db->tableExists('ticket_attachment') && $db->fieldExists('ticket_id', 'ticket_attachment')) {
                    $db->query("UPDATE `ticket_attachment` ta INNER JOIN `ticket_transactions` tt ON ta.ticket_id = tt.id SET ta.ticket_id = tt.new_uuid WHERE tt.new_uuid IS NOT NULL");
                }
            }

            // Gantikan kolom `id` lama dengan `new_uuid`
            try {
                $db->query("ALTER TABLE `{$tbl}` MODIFY COLUMN `id` VARCHAR(36) NOT NULL");
                $db->query("ALTER TABLE `{$tbl}` DROP PRIMARY KEY");
            } catch (\Throwable $e) {}

            try {
                $db->query("ALTER TABLE `{$tbl}` DROP COLUMN `id`");
            } catch (\Throwable $e) {}

            $db->query("ALTER TABLE `{$tbl}` CHANGE COLUMN `new_uuid` `id` VARCHAR(36) NOT NULL PRIMARY KEY FIRST");
        }

        $db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        // No-op rollback
    }

    private function generateUuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
