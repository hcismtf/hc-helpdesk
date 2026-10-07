<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Services\AuthService;

class PermissionSeeder extends Seeder
{
  public function run()
  {
    $authService = new AuthService();

    $permissions = [
      ['code' => 'ticket.read', 'name' => 'Ticket View'],
      ['code' => 'ticket.create', 'name' => 'Ticket Create'],
      ['code' => 'ticket.update', 'name' => 'Ticket Update'],
      ['code' => 'ticket.reply', 'name' => 'Ticket Reply'],
      ['code' => 'ticket.status', 'name' => 'Ticket Status'],

      ['code' => 'settings.read', 'name' => 'Settings View'],

      ['code' => 'faq.read', 'name' => 'FAQ View'],
      ['code' => 'faq.create', 'name' => 'FAQ Create'],
      ['code' => 'faq.update', 'name' => 'FAQ Update'],
      ['code' => 'faq.delete', 'name' => 'FAQ Delete'],

      ['code' => 'role.read', 'name' => 'Roles View'],
      ['code' => 'role.create', 'name' => 'Roles Create'],
      ['code' => 'role.update', 'name' => 'Role Update'],
      ['code' => 'role.delete', 'name' => 'Role Delete'],

      ['code' => 'request_type.read', 'name' => 'Request Type View'],
      ['code' => 'request_type.create', 'name' => 'Request Type Create'],
      ['code' => 'request_type.update', 'name' => 'Request Type Update'],
      ['code' => 'request_type.delete', 'name' => 'Request Type Delete'],

      ['code' => 'sla.read', 'name' => 'SLA View'],
      ['code' => 'sla.create', 'name' => 'SLA Create'],
      ['code' => 'sla.update', 'name' => 'SLA Update'],
      ['code' => 'sla.delete', 'name' => 'SLA Delete'],

      ['code' => 'permission.read', 'name' => 'Permissions View'],
      ['code' => 'permission.create', 'name' => 'Permission Create'],
      ['code' => 'permission.update', 'name' => 'Permission Update'],
      ['code' => 'permission.delete', 'name' => 'Permission Delete'],

      ['code' => 'user.read', 'name' => 'Users View'],
      ['code' => 'user.create', 'name' => 'User Create'],
      ['code' => 'user.update', 'name' => 'User Update'],
      ['code' => 'user.delete', 'name' => 'User Delete'],

      ['code' => 'report.read', 'name' => 'Reports View'],
      ['code' => 'report.export', 'name' => 'Reports Export'],
      ['code' => 'report.delete', 'name' => 'Reports Delete'],

      ['code' => 'dev.access', 'name' => 'Developer Access'],
    ];

    $now = date('Y-m-d H:i:s');
    $rows = [];

    foreach ($permissions as $p) {
      $rows[] = [
        'id' => $authService->generateUUIDv4(),
        'code' => $p['code'],
        'name' => $p['name'],
        'created_by' => 'SYSTEM',
        'created_date' => $now,
        'modified_date' => $now,
        'modified_by' => 'SYSTEM'
      ];
    }

    $this->db->table('permissions')->insertBatch($rows);
  }
}
