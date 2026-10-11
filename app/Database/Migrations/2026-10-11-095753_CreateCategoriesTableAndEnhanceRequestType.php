<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTableAndEnhanceRequestType extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Buat Tabel Categories (Main Categories / Topik Utama HC)
        if (!$this->db->tableExists('categories')) {
            $this->forge->addField([
                'id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 36,
                ],
                'code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'icon' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'fa-solid fa-folder',
                ],
                'color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => '#2563EB',
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'default'    => 0,
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'created_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'created_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'modified_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'modified_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('code');
            $this->forge->addKey('is_active');
            $this->forge->createTable('categories', true);
        }

        // 2. Tambah Kolom ke request_type untuk hierarki, SLA, dan Popular Service
        $fieldsToAdd = [];
        if (!$this->db->fieldExists('category_id', 'request_type')) {
            $fieldsToAdd['category_id'] = [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'id',
            ];
        }
        if (!$this->db->fieldExists('sla_id', 'request_type')) {
            $fieldsToAdd['sla_id'] = [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'description',
            ];
        }
        if (!$this->db->fieldExists('badge_tag', 'request_type')) {
            $fieldsToAdd['badge_tag'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'sla_id',
            ];
        }
        if (!$this->db->fieldExists('is_popular', 'request_type')) {
            $fieldsToAdd['is_popular'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'badge_tag',
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('request_type', $fieldsToAdd);
        }

        // 3. Masukkan Seed Data Kategori Utama HC (Gambar 3)
        $now = date('Y-m-d H:i:s');
        $defaultCategories = [
            [
                'id'           => 'cat-presensi',
                'code'         => 'PRESENSI',
                'name'         => 'Presensi & Kehadiran',
                'description'  => 'Koreksi absensi, lupa tap ID, pengajuan cuti mendesak, dinas luar & lembur.',
                'icon'         => 'fa-solid fa-calendar-check',
                'color'        => '#2563EB',
                'sort_order'   => 1,
                'is_active'    => 1,
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'cat-payroll',
                'code'         => 'PAYROLL',
                'name'         => 'Payroll & Penggajian',
                'description'  => 'Pertanyaan slip gaji, koreksi tunjangan/insentif, potongan PPh 21 & rekening.',
                'icon'         => 'fa-solid fa-money-bill-wave',
                'color'        => '#059669',
                'sort_order'   => 2,
                'is_active'    => 1,
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'cat-asuransi',
                'code'         => 'ASURANSI',
                'name'         => 'Klaim Asuransi & Benefit',
                'description'  => 'Klaim rawat inap/jalan, penambahan tanggungan, kartu asuransi & BPJS.',
                'icon'         => 'fa-solid fa-shield-heart',
                'color'        => '#D97706',
                'sort_order'   => 3,
                'is_active'    => 1,
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'cat-fasilitas',
                'code'         => 'FASILITAS',
                'name'         => 'Akun & Fasilitas HC',
                'description'  => 'Kendala akun HCIS/ESS, pergantian device presensi (IMEI), reset akses.',
                'icon'         => 'fa-solid fa-laptop-code',
                'color'        => '#7C3AED',
                'sort_order'   => 4,
                'is_active'    => 1,
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'cat-administrasi',
                'code'         => 'ADMINISTRASI',
                'name'         => 'Administrasi & Surat HC',
                'description'  => 'Permohonan surat keterangan kerja, rekomendasi visa, paklaring & update KK.',
                'icon'         => 'fa-solid fa-file-signature',
                'color'        => '#0284C7',
                'sort_order'   => 5,
                'is_active'    => 1,
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'cat-lainnya',
                'code'         => 'LAINNYA',
                'name'         => 'Lainnya / Umum',
                'description'  => 'Pertanyaan umum kepegawaian atau kendala di luar kategori di atas.',
                'icon'         => 'fa-solid fa-circle-question',
                'color'        => '#475569',
                'sort_order'   => 6,
                'is_active'    => 1,
                'created_by'   => 'system',
                'created_date' => $now,
            ]
        ];

        foreach ($defaultCategories as $cat) {
            $existing = $db->table('categories')->where('id', $cat['id'])->get()->getRow();
            if (!$existing) {
                $db->table('categories')->insert($cat);
            }
        }

        // 4. Masukkan Seed Data Layanan Populer & Sub Kategori ke request_type (Gambar 2)
        $popularTypes = [
            [
                'id'           => 'req-koreksi-presensi',
                'category_id'  => 'cat-presensi',
                'name'         => 'Koreksi Kehadiran & Presensi',
                'description'  => 'Sinkronisasi kehadiran HC Eazy, cuti khusus, dan jam kerja.',
                'badge_tag'    => 'PRESENSI',
                'is_popular'   => 1,
                'status'       => 'Active',
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'req-surat-keterangan',
                'category_id'  => 'cat-administrasi',
                'name'         => 'Surat Keterangan Kepegawaian',
                'description'  => 'Ajukan tiket untuk mengurus surat keterangan kepegawaian sesuai kebutuhan Anda.',
                'badge_tag'    => 'SURAT KETERANGAN',
                'is_popular'   => 1,
                'status'       => 'Active',
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'req-slip-gaji',
                'category_id'  => 'cat-payroll',
                'name'         => 'Slip Gaji & Bukti Pajak',
                'description'  => 'Permintaan bukti potong PPh 21 (1721-A1), klarifikasi tunjangan, dan rincian payroll berkala.',
                'badge_tag'    => 'PAYROLL',
                'is_popular'   => 1,
                'status'       => 'Active',
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'req-surat-kerja',
                'category_id'  => 'cat-administrasi',
                'name'         => 'Surat Keterangan Kerja',
                'description'  => 'Penerbitan surat pengantar bank, pengajuan KPR, visa dinas, dan verifikasi kepegawaian.',
                'badge_tag'    => 'ADMINISTRASI',
                'is_popular'   => 1,
                'status'       => 'Active',
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'req-klaim-asuransi',
                'category_id'  => 'cat-asuransi',
                'name'         => 'Klaim Rawat Inap & BPJS',
                'description'  => 'Pengajuan reimbursement rawat inap/jalan dan koordinasi manfaat BPJS.',
                'badge_tag'    => 'ASURANSI',
                'is_popular'   => 0,
                'status'       => 'Active',
                'created_by'   => 'system',
                'created_date' => $now,
            ],
            [
                'id'           => 'req-kendala-hcis',
                'category_id'  => 'cat-fasilitas',
                'name'         => 'Reset Akun & IMEI HCIS',
                'description'  => 'Permohonan pergantian IMEI perangkat presensi mobile atau reset password portal HCIS.',
                'badge_tag'    => 'FASILITAS',
                'is_popular'   => 0,
                'status'       => 'Active',
                'created_by'   => 'system',
                'created_date' => $now,
            ]
        ];

        foreach ($popularTypes as $pt) {
            $existing = $db->table('request_type')->where('id', $pt['id'])->orWhere('name', $pt['name'])->get()->getRow();
            if (!$existing) {
                $db->table('request_type')->insert($pt);
            } else {
                $db->table('request_type')->where('id', $existing->id)->update([
                    'category_id' => $pt['category_id'],
                    'badge_tag'   => $pt['badge_tag'],
                    'is_popular'  => $pt['is_popular'],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('categories')) {
            $this->forge->dropTable('categories', true);
        }
        if ($this->db->fieldExists('category_id', 'request_type')) {
            $this->forge->dropColumn('request_type', ['category_id', 'sla_id', 'badge_tag', 'is_popular']);
        }
    }
}
