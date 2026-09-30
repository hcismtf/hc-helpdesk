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
        if (!session('isLoggedIn')) {
            $isAjax = ($request instanceof \CodeIgniter\HTTP\IncomingRequest && $request->isAJAX())
                || service('request')->isAJAX();

            if ($isAjax) {
                return service('response')->setStatusCode(401)->setJSON([
                    'status'  => 'error',
                    'message' => 'Sesi Anda telah berakhir, silakan login kembali.'
                ]);
            }

            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}
