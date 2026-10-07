<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is logged in before accessing admin routes
     *
     * @param \CodeIgniter\HTTP\IncomingRequest|\CodeIgniter\HTTP\RequestInterface $request
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $isAjax = ($request instanceof \CodeIgniter\HTTP\IncomingRequest && $request->isAJAX())
            || service('request')->isAJAX();

        // 1. Wajib login
        if (!session('isLoggedIn')) {
            if ($isAjax) {
                return service('response')->setStatusCode(401)->setJSON([
                    'status'  => 'error',
                    'message' => 'Sesi Anda telah berakhir, silakan login kembali.'
                ]);
            }

            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Wajib memiliki role_id (tidak boleh kosong / null / 0)
        $roleId = session('role_id');
        $role = strtolower(session('role') ?? '');
        $isSuperadmin = ($role === 'superadmin');

        if (!$isSuperadmin && (empty($roleId) || $role === 'user')) {
            if ($isAjax) {
                return service('response')->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Akses ditolak: Akun Anda tidak memiliki hak akses administrator (role_id tidak ditemukan).'
                ]);
            }

            return redirect()->to('/')->with('error', 'Akses ditolak: Akun Anda tidak memiliki role administratif untuk mengakses halaman admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}
