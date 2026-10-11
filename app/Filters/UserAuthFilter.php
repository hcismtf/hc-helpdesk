<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UserAuthFilter implements FilterInterface
{
    /**
     * Memastikan pengguna telah login sebagai karyawan atau admin sebelum mengakses fitur tiket pengguna
     *
     * @param RequestInterface $request
     * @param mixed $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $isAjax = ($request instanceof \CodeIgniter\HTTP\IncomingRequest && $request->isAJAX())
            || service('request')->isAJAX();

        // Wajib login (memiliki sesi login aktif dan identitas karyawan/user_id)
        if (!session('isLoggedIn') || (!session('user_id') && !session('employee_no'))) {
            if ($isAjax) {
                return service('response')->setStatusCode(401)->setJSON([
                    'status'  => 'error',
                    'message' => 'Sesi Anda telah berakhir atau belum login. Silakan login terlebih dahulu.'
                ]);
            }

            return redirect()->to(base_url('/'))->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk mengakses tiket.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada mutasi setelah request
    }
}
