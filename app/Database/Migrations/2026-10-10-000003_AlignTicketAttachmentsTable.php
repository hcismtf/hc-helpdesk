<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlignTicketAttachmentsTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Tambahkan / sesuaikan kolom pada tabel ticket_attachment
        if ($db->tableExists('ticket_attachment')) {
            // Sinkronisasi tiket_trx_id -> ticket_id
            if (!$db->fieldExists('ticket_id', 'ticket_attachment')) {
                if ($db->fieldExists('tiket_trx_id', 'ticket_attachment')) {
                    $this->forge->addColumn('ticket_attachment', [
                        'ticket_id' => [
                            'type'       => 'VARCHAR',
                            'constraint' => 50,
                            'null'       => true,
                            'after'      => 'id',
                        ],
                    ]);
                    $db->query("UPDATE `ticket_attachment` SET `ticket_id` = `tiket_trx_id` WHERE `ticket_id` IS NULL");
                } else {
                    $this->forge->addColumn('ticket_attachment', [
                        'ticket_id' => [
                            'type'       => 'VARCHAR',
                            'constraint' => 50,
                            'null'       => true,
                            'after'      => 'id',
                        ],
                    ]);
                }
            }

            // Tambahkan kolom comment_id
            if (!$db->fieldExists('comment_id', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'comment_id' => [
                        'type'       => 'BIGINT',
                        'constraint' => 20,
                        'null'       => true,
                        'after'      => 'ticket_id',
                        'comment'    => 'ID respon/komentar jika berkas diunggah saat membalas',
                    ],
                ]);
            }

            // Tambahkan file_size
            if (!$db->fieldExists('file_size', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'file_size' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'null'       => true,
                        'after'      => 'file_path',
                        'comment'    => 'Ukuran berkas dalam bytes/KB',
                    ],
                ]);
            }

            // Tambahkan mime_type
            if (!$db->fieldExists('mime_type', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'mime_type' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 100,
                        'null'       => true,
                        'after'      => 'file_size',
                        'comment'    => 'Tipe MIME berkas (image/png, application/pdf, dll)',
                    ],
                ]);
            }

            // Tambahkan file_type jika belum ada (alias)
            if (!$db->fieldExists('file_type', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'file_type' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 100,
                        'null'       => true,
                        'after'      => 'mime_type',
                    ],
                ]);
            }

            // Tambahkan uploaded_by
            if (!$db->fieldExists('uploaded_by', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'uploaded_by' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 50,
                        'null'       => true,
                        'after'      => 'file_type',
                        'comment'    => 'FK/ID user yang mengunggah berkas',
                    ],
                ]);
            }

            // Tambahkan created_at
            if (!$db->fieldExists('created_at', 'ticket_attachment')) {
                $this->forge->addColumn('ticket_attachment', [
                    'created_at' => [
                        'type'    => 'TIMESTAMP',
                        'null'    => true,
                        'after'   => 'uploaded_by',
                    ],
                ]);
                $db->query("UPDATE `ticket_attachment` SET `created_at` = `created_date` WHERE `created_at` IS NULL AND `created_date` IS NOT NULL");
            }

            // Indexes
            try {
                $db->query("CREATE INDEX `idx_ta_ticket_id` ON `ticket_attachment` (`ticket_id`)");
            } catch (\Throwable $e) {}
            try {
                $db->query("CREATE INDEX `idx_ta_comment_id` ON `ticket_attachment` (`comment_id`)");
            } catch (\Throwable $e) {}
            try {
                $db->query("CREATE INDEX `idx_ta_uploaded_by` ON `ticket_attachment` (`uploaded_by`)");
            } catch (\Throwable $e) {}

            // Buat VIEW alias ticket_attachments agar tabel jamak/tunggal keduanya kompatibel 100%
            try {
                $db->query("CREATE OR REPLACE VIEW `ticket_attachments` AS SELECT * FROM `ticket_attachment`");
            } catch (\Throwable $e) {}
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        try {
            $db->query("DROP VIEW IF EXISTS `ticket_attachments`");
        } catch (\Throwable $e) {}

        if ($db->tableExists('ticket_attachment')) {
            $this->forge->dropColumn('ticket_attachment', [
                'comment_id',
                'file_size',
                'mime_type',
                'file_type',
                'uploaded_by',
                'created_at',
            ]);
        }
    }
}
