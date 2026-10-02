<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\RoleDetailModel;
use App\Models\RoleModel;

class UserSyncService
{
    private UserModel $userModel;
    private RoleDetailModel $roleDetailModel;
    private RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleDetailModel = new RoleDetailModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Upsert user lokal CI4 dari data payload Spring Boot
     */
    public function syncUserHCEazy(string $employeeNo, array $dto): array
    {
        // Cari user berdasarkan employee_no atau email
        $user = $this->userModel
            ->where('employee_no', $employeeNo)
            ->orWhere('email', $dto['officeMail'] ?? '')
            ->first();

        $userData = [
            'employee_no'     => $employeeNo,
            'name'            => $dto['fullName'] ?? $dto['name'] ?? $employeeNo,
            'email'           => $dto['officeMail'] ?? null,
            'position'        => $dto['position'] ?? null,
            'position_level'  => $dto['positionLevel'] ?? null,
            'job_title'       => $dto['jobTitle'] ?? null,
            'created_by'      => "SYSTEM",
            'modified_by'     => "SYSTEM",
            'last_login_time' => date('Y-m-d H:i:s'),
            'is_deleted'      => 0
        ];

        if ($user) {
            $this->userModel->update($user['id'], $userData);
            $userId = $user['id'];
        } else {
            // Password diisi dummy hash karena autentikasi terpusat di Keycloak
            $userData['password'] = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
            $userId = $this->userModel->insert($userData);

            // Assign default role jika user baru (misal role 'User' / 'Employee')
            $defaultRole = $this->roleModel->where('name', 'User')->first();
            if ($defaultRole) {
                $this->roleDetailModel->insert([
                    'user_id' => $userId,
                    'role_id' => $defaultRole['id']
                ]);
            }
        }

        return $this->userModel->find($userId);
    }
}