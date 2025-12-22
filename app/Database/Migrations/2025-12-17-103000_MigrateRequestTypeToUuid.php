<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateRequestTypeToUuid extends Migration
{
    public function up()
    {
        // 1. Tambah kolom id_uuid temporary
        $this->forge->addColumn('request_type', [
            'id_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
                'null'       => true,
            ]
        ]);

        // 2. Generate UUID untuk setiap request_type yang ada
        $db = \Config\Database::connect();
        $requestTypes = $db->table('request_type')->get()->getResultArray();
        
        foreach ($requestTypes as $rt) {
            $uuid = $this->generateUUID();
            $db->table('request_type')
               ->where('id', $rt['id'])
               ->update(['id_uuid' => $uuid]);
        }

        // 3. Drop foreign key dari tiket_trx (jika ada)
        $this->db->disableForeignKeyChecks();
        
        // 4. Drop primary key lama dan ubah id jadi regular column
        $this->db->query('ALTER TABLE `request_type` DROP PRIMARY KEY');
        
        // 5. Ubah id_uuid jadi primary key
        $this->db->query('ALTER TABLE `request_type` 
                          ADD PRIMARY KEY (`id_uuid`), 
                          DROP COLUMN `id`, 
                          CHANGE COLUMN `id_uuid` `id` VARCHAR(36) NOT NULL');
        
        // 6. Update tiket_trx.req_type ke UUID
        // First, create temp column
        $this->forge->addColumn('tiket_trx', [
            'req_type_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
                'null'       => true,
            ]
        ]);

        // Update dengan UUID dari request_type
        $this->db->query('
            UPDATE tiket_trx tt
            JOIN request_type rt ON tt.req_type = rt.name OR CAST(tt.req_type AS CHAR) = rt.id
            SET tt.req_type_uuid = rt.id
        ');

        // Drop old column dan rename
        $this->db->query('ALTER TABLE `tiket_trx` DROP COLUMN `req_type`, CHANGE COLUMN `req_type_uuid` `req_type` VARCHAR(36)');

        // 7. Add foreign key constraint
        $this->forge->addForeignKey('req_type', 'request_type', 'id', '', 'CASCADE', false);
        
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();
        
        // Drop foreign key
        $this->db->query('ALTER TABLE `tiket_trx` DROP FOREIGN KEY tiket_trx_req_type_foreign');
        
        // 1. Tambah id_old temporary
        $this->forge->addColumn('request_type', [
            'id_old' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ]
        ]);

        // 2. Restore old IDs
        $db = \Config\Database::connect();
        $this->db->query("
            UPDATE request_type rt
            SET rt.id_old = (
                SELECT SUBSTRING(rt.id, 1, 2)
            )
        ");

        // 3. Drop primary key dan restore
        $this->db->query('ALTER TABLE `request_type` DROP PRIMARY KEY');
        $this->db->query('ALTER TABLE `request_type` 
                          DROP COLUMN `id`,
                          CHANGE COLUMN `id_old` `id` INT(11) NOT NULL AUTO_INCREMENT,
                          ADD PRIMARY KEY (`id`)');

        // 4. Restore tiket_trx.req_type
        $this->forge->addColumn('tiket_trx', [
            'req_type_old' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ]
        ]);

        $this->db->query('
            UPDATE tiket_trx tt
            JOIN request_type rt ON tt.req_type = rt.id
            SET tt.req_type_old = rt.name
        ');

        $this->db->query('ALTER TABLE `tiket_trx` DROP COLUMN `req_type`, CHANGE COLUMN `req_type_old` `req_type` VARCHAR(100)');

        $this->db->enableForeignKeyChecks();
    }

    private function generateUUID()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
