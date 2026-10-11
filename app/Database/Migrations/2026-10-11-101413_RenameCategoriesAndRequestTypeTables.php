<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameCategoriesAndRequestTypeTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Rename tabel categories -> request_category
        if ($this->db->tableExists('categories') && !$this->db->tableExists('request_category')) {
            $this->forge->renameTable('categories', 'request_category');
        } elseif (!$this->db->tableExists('request_category')) {
            // Jika belum ada tabel sama sekali, buat request_category
            $this->forge->addField([
                'id'            => ['type' => 'VARCHAR', 'constraint' => 36],
                'code'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'name'          => ['type' => 'VARCHAR', 'constraint' => 150],
                'description'   => ['type' => 'TEXT', 'null' => true],
                'icon'          => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'fa-solid fa-folder'],
                'color'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '#2563EB'],
                'sort_order'    => ['type' => 'INT', 'constraint' => 5, 'default' => 0],
                'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_by'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'created_date'  => ['type' => 'DATETIME', 'null' => true],
                'modified_by'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'modified_date' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('request_category', true);
        }

        // 2. Rename tabel request_type -> request_sub_category
        if ($this->db->tableExists('request_type') && !$this->db->tableExists('request_sub_category')) {
            $this->forge->renameTable('request_type', 'request_sub_category');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('request_category') && !$this->db->tableExists('categories')) {
            $this->forge->renameTable('request_category', 'categories');
        }
        if ($this->db->tableExists('request_sub_category') && !$this->db->tableExists('request_type')) {
            $this->forge->renameTable('request_sub_category', 'request_type');
        }
    }
}
