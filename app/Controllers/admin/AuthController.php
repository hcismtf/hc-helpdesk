<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AuthService;
use Config\Superadmin;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show admin login view or redirect if already logged in
     */
    public function login()
    {
        if (session('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin/login');
    }

    /**
     * Handle login authentication
     */
    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username dan password tidak boleh kosong');
        }

        // 1. Superadmin login (config)
        $superadminConfig = new Superadmin();
        if (
            $username === $superadminConfig->username &&
            $password === $superadminConfig->password_plain
        ) {
            session()->set([
                'isLoggedIn'       => true,
                'role'             => 'superadmin',
                'username'         => $username,
                'user_permissions' => ['dashboard', 'tickets', 'user_management', 'system_settings', 'reports']
            ]);
            return redirect()->to('/admin/dashboard');
        }

        // 2. User login from database
        $userModel = new UserModel();
        $user = $userModel
            ->groupStart()
                ->where('email', $username)
                ->orWhere('name', $username)
            ->groupEnd()
            ->groupStart()
                ->where('is_deleted !=', 1)
                ->orWhere('is_deleted IS NULL')
            ->groupEnd()
            ->first();

        log_message('info', 'Login attempt - Username: ' . $username . ', User found: ' . ($user ? 'YES' : 'NO'));

        if ($user && $this->authService->verifyAndUpgradePassword($password, $user, $userModel)) {
            $roleData = $this->authService->getUserRoleAndPermissions($user['id']);

            $userModel->update($user['id'], [
                'last_login_time' => date('Y-m-d H:i:s')
            ]);

            session()->set([
                'isLoggedIn'       => true,
                'role'             => $roleData['role_name'],
                'role_name'        => $roleData['role_name'],
                'username'         => $user['name'],
                'user_id'          => $user['id'],
                'user_permissions' => $roleData['permissions']
            ]);

            return redirect()->to('/admin/dashboard');
        }

        return redirect()->back()->with('error', 'Username atau password salah');
    }

    /**
     * Handle admin logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }

    /**
     * Display 403 forbidden error page
     */
    public function forbidden()
    {
        return $this->response->setStatusCode(403)->setBody(
            view('errors/html/error_403', ['message' => 'Anda tidak memiliki hak akses untuk membuka halaman ini.'])
        );
    }
}
