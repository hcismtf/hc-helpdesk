<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Services\AuthService;

class RoleSeeder extends Seeder
{
  public function run()
  {
    $authService = new AuthService();
    $db = $this->db;
    $now = date('Y-m-d H:i:s');

    // 1. Ambil semua permissions yang sudah di-seed sebelumnya
    $allPermissions = $db->table('permissions')->get()->getResultArray();
    $permissionMap = array_column($allPermissions, 'id', 'code'); // ['ticket:read' => 'uuid-...']

    // 2. Daftar Definisi Role
    $rolesData = [
      [
        'id' => $authService->generateUUIDv4(),
        'name' => 'Super Admin',
      ],
      [
        'id' => $authService->generateUUIDv4(),
        'name' => 'Department Head',
      ],
      [
        'id' => $authService->generateUUIDv4(),
        'name' => 'Helpdesk L1',
      ],
      [
        'id' => $authService->generateUUIDv4(),
        'name' => 'PIC Dev / BA',
      ],
      [
        'id' => $authService->generateUUIDv4(),
        'name' => 'Employee',
      ],
    ];

    // Insert Roles
    $roleRows = [];
    $roleIdByName = [];
    foreach ($rolesData as $role) {
      $roleRows[] = [
        'id' => $role['id'],
        'name' => $role['name'],
        'created_by' => 'SYSTEM',
        'created_date' => $now,
        'modified_date' => $now,
      ];
      $roleIdByName[$role['name']] = $role['id'];
    }
    $db->table('role')->insertBatch($roleRows);

    // 3. Mapping Matrix Role ke Permission
    $rolePermissionRows = [];

    // --- A. SUPER ADMIN: Dapat SEMUA permission tanpa kecuali ---
    $superAdminRoleId = $roleIdByName['Super Admin'];
    foreach ($allPermissions as $perm) {
      $rolePermissionRows[] = [
        'id' => $authService->generateUUIDv4(),
        'role_id' => $superAdminRoleId,
        'permission_id' => $perm['id'],
        'created_by' => 'SYSTEM',
        'created_date' => $now,
        'modified_date' => $now,
      ];
    }

    // --- B. HELPDESK L1: Pengelolaan Tiket, Respon, dan View SLA ---
    $l1RoleId = $roleIdByName['Helpdesk L1'];
    $l1Permissions = [
      'ticket:read',
      'ticket:update',
      'ticket:reply',
      'ticket:status',
      'faq:read',
      'sla:read',
      'request_type:read',
      'report:read'
    ];
    foreach ($l1Permissions as $code) {
      if (isset($permissionMap[$code])) {
        $rolePermissionRows[] = [
          'id' => $authService->generateUUIDv4(),
          'role_id' => $l1RoleId,
          'permission_id' => $permissionMap[$code],
          'created_by' => 'SYSTEM',
          'created_date' => $now,
          'modified_date' => $now,
        ];
      }
    }

    // --- C. PIC DEV / BA: Eksekusi Tiket Eskalasi Teknis ---
    $devRoleId = $roleIdByName['PIC Dev / BA'];
    $devPermissions = [
      'ticket:read',
      'ticket:update',
      'ticket:reply',
      'ticket:status',
      'faq:read',
      'sla:read',
      'request_type:read'
    ];
    foreach ($devPermissions as $code) {
      if (isset($permissionMap[$code])) {
        $rolePermissionRows[] = [
          'id' => $authService->generateUUIDv4(),
          'role_id' => $devRoleId,
          'permission_id' => $permissionMap[$code],
          'created_by' => 'SYSTEM',
          'created_date' => $now,
          'modified_date' => $now,
        ];
      }
    }

    // --- D. EMPLOYEE: Hanya Buat & Lihat Tiket Milik Sendiri ---
    $empRoleId = $roleIdByName['Employee'];
    $empPermissions = ['ticket:create', 'ticket:read', 'faq:read'];
    foreach ($empPermissions as $code) {
      if (isset($permissionMap[$code])) {
        $rolePermissionRows[] = [
          'id' => $authService->generateUUIDv4(),
          'role_id' => $empRoleId,
          'permission_id' => $permissionMap[$code],
          'created_by' => 'SYSTEM',
          'created_date' => $now,
          'modified_date' => $now,
        ];
      }
    }

    $userModel = new \App\Models\UserModel();
    $targetEmployeeNo = '00009004';

    $existingUser = $userModel->where('employee_no', $targetEmployeeNo)->first();

    if ($existingUser) {
      $userModel->update($existingUser->id, [
        'role_id' => $superAdminRoleId,
        'position_level' => 'SUPERADMIN',
        'category_id' => null,
        'status' => 'ACTIVE',
        'modified_by' => 'SYSTEM',
        'modified_date' => $now,
      ]);
    }
  }
}
