<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoryAndRequestTypeSeeder extends Seeder
{
    private function generateUuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Pastikan tabel yang digunakan: request_category dan request_sub_category (fallback jika belum di-rename)
        $categoryTable = $db->tableExists('request_category') ? 'request_category' : 'categories';
        $subCategoryTable = $db->tableExists('request_sub_category') ? 'request_sub_category' : 'request_type';

        // 1. Ambil ID SLA yang ada untuk relasi (Urgent, High, Medium, Low)
        $slas = $db->table('sla')->get()->getResultArray();
        $slaMap = [];
        foreach ($slas as $s) {
            $slaMap[strtolower($s['priority'])] = $s['id'];
        }

        $slaUrgent = $slaMap['urgent'] ?? null;
        $slaHigh   = $slaMap['high'] ?? null;
        $slaMedium = $slaMap['medium'] ?? null;
        $slaLow    = $slaMap['low'] ?? null;

        // Bersihkan data lama dengan ID string biasa 'cat-*' atau 'req-*' agar bersih menjadi UUIDv4 murni
        $db->table($subCategoryTable)->where('id LIKE', 'req-%')->delete();
        $db->table($categoryTable)->where('id LIKE', 'cat-%')->delete();

        // 2. Data Master Request Category (Pilar Topik HC - UUID v4)
        $categoriesData = [
            'presensi' => [
                'code'         => 'PRESENSI',
                'name'         => 'Presensi & Kehadiran',
                'description'  => 'Koreksi absensi, lupa tap ID, pengajuan cuti mendesak, dinas luar & lembur.',
                'icon'         => 'fa-solid fa-calendar-check',
                'color'        => '#2563EB',
                'sort_order'   => 1,
            ],
            'payroll' => [
                'code'         => 'PAYROLL',
                'name'         => 'Payroll & Penggajian',
                'description'  => 'Pertanyaan slip gaji, koreksi tunjangan/insentif, potongan PPh 21 & rekening.',
                'icon'         => 'fa-solid fa-money-bill-wave',
                'color'        => '#059669',
                'sort_order'   => 2,
            ],
            'asuransi' => [
                'code'         => 'ASURANSI',
                'name'         => 'Klaim Asuransi & Benefit',
                'description'  => 'Klaim rawat inap/jalan, penambahan tanggungan, kartu asuransi & BPJS.',
                'icon'         => 'fa-solid fa-shield-heart',
                'color'        => '#D97706',
                'sort_order'   => 3,
            ],
            'fasilitas' => [
                'code'         => 'FASILITAS',
                'name'         => 'Akun & Fasilitas HC',
                'description'  => 'Kendala akun HCIS/ESS, pergantian device presensi (IMEI), reset akses.',
                'icon'         => 'fa-solid fa-laptop-code',
                'color'        => '#7C3AED',
                'sort_order'   => 4,
            ],
            'administrasi' => [
                'code'         => 'ADMINISTRASI',
                'name'         => 'Administrasi & Surat HC',
                'description'  => 'Permohonan surat keterangan kerja, rekomendasi visa, paklaring & update KK.',
                'icon'         => 'fa-solid fa-file-signature',
                'color'        => '#0284C7',
                'sort_order'   => 5,
            ],
            'hubungan_kerja' => [
                'code'         => 'HUBUNGAN_KERJA',
                'name'         => 'Hubungan Kerja & Konsultasi',
                'description'  => 'Konsultasi ketenagakerjaan, mediasi internal, etika kerja, dan perpanjangan kontrak.',
                'icon'         => 'fa-solid fa-handshake-simple',
                'color'        => '#EA580C',
                'sort_order'   => 6,
            ],
            'lainnya' => [
                'code'         => 'LAINNYA',
                'name'         => 'Lainnya / Umum',
                'description'  => 'Pertanyaan umum kepegawaian atau kendala di luar kategori di atas.',
                'icon'         => 'fa-solid fa-circle-question',
                'color'        => '#475569',
                'sort_order'   => 7,
            ],
        ];

        $categoryUuidMap = [];

        foreach ($categoriesData as $alias => $data) {
            $existing = $db->table($categoryTable)->where('name', $data['name'])->get()->getRow();
            if ($existing) {
                $catId = $existing->id;
                // Jika ID existing belum UUID valid (36 chars dengan strip)
                if (strlen($catId) !== 36) {
                    $catId = $this->generateUuidV4();
                    $db->table($categoryTable)->where('id', $existing->id)->update([
                        'id' => $catId,
                    ]);
                }
                $db->table($categoryTable)->where('id', $catId)->update(array_merge($data, [
                    'is_active'     => 1,
                    'modified_by'   => 'system',
                    'modified_date' => $now,
                ]));
            } else {
                $catId = $this->generateUuidV4();
                $db->table($categoryTable)->insert(array_merge($data, [
                    'id'           => $catId,
                    'is_active'    => 1,
                    'created_by'   => 'system',
                    'created_date' => $now,
                    'modified_by'  => 'system',
                    'modified_date'=> $now,
                ]));
            }
            $categoryUuidMap[$alias] = $catId;
        }

        // 3. Data Request Sub Category (Layanan Spesifik - UUID v4)
        $subCategories = [
            // --- Presensi & Kehadiran ---
            [
                'cat_alias'   => 'presensi',
                'name'        => 'Koreksi Kehadiran & Presensi',
                'description' => 'Sinkronisasi kehadiran HC Eazy, lupa tap absensi, cuti mendesak, dan lembur.',
                'sla_id'      => $slaMedium,
                'badge_tag'   => 'PRESENSI',
                'is_popular'  => 1,
                'status'      => 'Active',
            ],
            [
                'cat_alias'   => 'presensi',
                'name'        => 'Pengajuan Dinas Luar Kota',
                'description' => 'Verifikasi dan pelaporan perjalanan dinas luar wilayah penugasan operasional.',
                'sla_id'      => $slaMedium,
                'badge_tag'   => 'PRESENSI',
                'is_popular'  => 0,
                'status'      => 'Active',
            ],

            // --- Payroll & Penggajian ---
            [
                'cat_alias'   => 'payroll',
                'name'        => 'Slip Gaji & Bukti Pajak',
                'description' => 'Permintaan bukti potong PPh 21 (1721-A1), klarifikasi tunjangan, dan rincian payroll.',
                'sla_id'      => $slaMedium,
                'badge_tag'   => 'PAYROLL',
                'is_popular'  => 1,
                'status'      => 'Active',
            ],
            [
                'cat_alias'   => 'payroll',
                'name'        => 'Perubahan Rekening Payroll',
                'description' => 'Pengajuan penggantian nomor rekening penerimaan gaji karyawan MTF.',
                'sla_id'      => $slaHigh,
                'badge_tag'   => 'PAYROLL',
                'is_popular'  => 0,
                'status'      => 'Active',
            ],

            // --- Klaim Asuransi & Benefit ---
            [
                'cat_alias'   => 'asuransi',
                'name'        => 'Klaim Rawat Inap & BPJS',
                'description' => 'Pengajuan reimbursement rawat inap/jalan dan koordinasi jaminan BPJS Kesehatan.',
                'sla_id'      => $slaMedium,
                'badge_tag'   => 'ASURANSI',
                'is_popular'  => 0,
                'status'      => 'Active',
            ],
            [
                'cat_alias'   => 'asuransi',
                'name'        => 'Penambahan Data Tanggungan Asuransi',
                'description' => 'Pendaftaran anggota keluarga baru (anak/istri/suami) pada asuransi kesehatan MTF.',
                'sla_id'      => $slaLow,
                'badge_tag'   => 'ASURANSI',
                'is_popular'  => 0,
                'status'      => 'Active',
            ],

            // --- Akun & Fasilitas HC ---
            [
                'cat_alias'   => 'fasilitas',
                'name'        => 'Reset Akun & IMEI HCIS',
                'description' => 'Permohonan pergantian IMEI ponsel presensi mobile HC Eazy atau reset kata sandi.',
                'sla_id'      => $slaUrgent,
                'badge_tag'   => 'FASILITAS',
                'is_popular'  => 0,
                'status'      => 'Active',
            ],

            // --- Administrasi & Surat HC ---
            [
                'cat_alias'   => 'administrasi',
                'name'        => 'Surat Keterangan Kepegawaian',
                'description' => 'Ajukan permohonan surat keterangan aktif bekerja untuk keperluan instansi atau visa.',
                'sla_id'      => $slaMedium,
                'badge_tag'   => 'SURAT KETERANGAN',
                'is_popular'  => 1,
                'status'      => 'Active',
            ],
            [
                'cat_alias'   => 'administrasi',
                'name'        => 'Surat Keterangan Kerja (KPR & Bank)',
                'description' => 'Penerbitan surat pengantar bank pengajuan kredit KPR, kartu kredit, dan verifikasi perbankan.',
                'sla_id'      => $slaMedium,
                'badge_tag'   => 'ADMINISTRASI',
                'is_popular'  => 1,
                'status'      => 'Active',
            ],

            // --- Hubungan Kerja & Konsultasi ---
            [
                'cat_alias'   => 'hubungan_kerja',
                'name'        => 'Konsultasi & Pengaduan HR',
                'description' => 'Saluran konfidensial hubungan industrial, etika kerja, dan mediasi permasalahan internal.',
                'sla_id'      => $slaHigh,
                'badge_tag'   => 'HUBUNGAN KERJA',
                'is_popular'  => 1,
                'status'      => 'Active',
            ],

            // --- Lainnya / Umum ---
            [
                'cat_alias'   => 'lainnya',
                'name'        => 'Konsultasi & Pertanyaan Umum HC',
                'description' => 'Informasi umum seputar kebijakan kepegawaian PT Mandiri Tunas Finance.',
                'sla_id'      => $slaLow,
                'badge_tag'   => 'UMUM',
                'is_popular'  => 0,
                'status'      => 'Active',
            ],
        ];

        foreach ($subCategories as $sub) {
            $catId = $categoryUuidMap[$sub['cat_alias']] ?? null;
            $existing = $db->table($subCategoryTable)->where('name', $sub['name'])->get()->getRow();

            $payload = [
                'category_id'   => $catId,
                'name'          => $sub['name'],
                'description'   => $sub['description'],
                'sla_id'        => $sub['sla_id'],
                'badge_tag'     => $sub['badge_tag'],
                'is_popular'    => $sub['is_popular'],
                'status'        => $sub['status'],
                'modified_by'   => 'system',
                'modified_date' => $now,
            ];

            if ($existing) {
                $subId = $existing->id;
                if (strlen($subId) !== 36) {
                    $subId = $this->generateUuidV4();
                    $payload['id'] = $subId;
                }
                $db->table($subCategoryTable)->where('id', $existing->id)->update($payload);
            } else {
                $payload['id']           = $this->generateUuidV4();
                $payload['created_by']   = 'system';
                $payload['created_date'] = $now;
                $db->table($subCategoryTable)->insert($payload);
            }
        }
    }
}
