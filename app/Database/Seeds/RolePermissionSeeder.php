<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Services\AuthService;

class RolePermissionSeeder extends Seeder
{
  public function run()
  {
    $authService = new AuthService();
    $db = $this->db;
    $now = date('Y-m-d H:i:s');

    // 1. Ambil data Roles dari database
    $roles = $db->table('role')->get()->getResultArray();
    if (empty($roles)) {
      echo "Error: Tabel roles masih kosong. Jalankan RoleSeeder terlebih dahulu.\n";
      return;
    }
    $roleMap = array_column($roles, 'id', 'name');

    // 2. Ambil data Permissions dari database
    $permissions = $db->table('permissions')->get()->getResultArray();
    if (empty($permissions)) {
      echo "Error: Tabel permissions masih kosong. Jalankan PermissionSeeder terlebih dahulu.\n";
      return;
    }
    $permissionMap = array_column($permissions, 'id', 'code');

    $insertBatch = [];

    // Helper untuk memasukkan data pivot
    $addPermissionToRole = function (string $roleName, array $permissionCodes) use (&$insertBatch, $roleMap, $permissionMap, $authService, $now) {
      if (!isset($roleMap[$roleName])) {
        return;
      }

      $roleId = $roleMap[$roleName];

      foreach ($permissionCodes as $code) {
        if (isset($permissionMap[$code])) {
          $insertBatch[] = [
            'id' => $authService->generateUUIDv4(),
            'role_id' => $roleId,
            'permission_id' => $permissionMap[$code],
            'created_by' => 'SYSTEM',
            'modified_by' => 'SYSTEM',
            'created_date' => $now,
            'modified_date' => $now,
          ];
        }
      }
    };

    // --- A. SUPER ADMIN (Dapatkan SEMUA Permission) ---
    if (isset($roleMap['Super Admin'])) {
      $superAdminRoleId = $roleMap['Super Admin'];
      foreach ($permissions as $perm) {
        $insertBatch[] = [
          'id' => $authService->generateUUIDv4(),
          'role_id' => $superAdminRoleId,
          'permission_id' => $perm['id'],
          'created_by' => 'SYSTEM',
          'modified_by' => 'SYSTEM',
          'created_date' => $now,
          'modified_date' => $now,
        ];
      }
    }

    // --- B. DEPARTMENT HEAD ---
    $addPermissionToRole('Department Head', [
      'ticket:read',
      'ticket:update',
      'ticket:reply',
      'ticket:status',
      'faq:read',
      'sla:read',
      'request_type:read',
      'report:read',
      'report:export',
    ]);

    // --- C. HELPDESK L1 ---
    $addPermissionToRole('Helpdesk L1', [
      'ticket:read',
      'ticket:update',
      'ticket:reply',
      'ticket:status',
      'faq:read',
      'sla:read',
      'request_type:read',
      'report:read',
    ]);

    // --- D. PIC DEV / BA ---
    $addPermissionToRole('PIC Dev / BA', [
      'ticket:read',
      'ticket:update',
      'ticket:reply',
      'ticket:status',
      'faq:read',
      'sla:read',
      'request_type:read',
    ]);

    // --- E. EMPLOYEE / USER BIASA ---
    $addPermissionToRole('Employee', [
      'ticket:read',
      'ticket:create',
      'faq:read',
    ]);

    // 3. Eksekusi Insert Batch
    if (!empty($insertBatch)) {
      // Gunakan chunk jika permissions sangat banyak agar tidak kena limit placeholder SQL
      $chunks = array_chunk($insertBatch, 100);
      foreach ($chunks as $chunk) {
        $db->table('role_permissions')->insertBatch($chunk);
      }
      echo "Berhasil memasukkan " . count($insertBatch) . " data role_permissions.\n";
    }
  }
}
