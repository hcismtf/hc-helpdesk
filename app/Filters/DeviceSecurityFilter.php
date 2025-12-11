<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DeviceSecurityFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip check untuk admin routes
        $uri = $request->getPath();
        if (strpos($uri, '/admin') === 0 || strpos($uri, 'index.php/admin') !== false) {
            return null; // Allow admin akses
        }

        // Deteksi root/jailbreak/developer mode
        if ($this->isDeviceCompromised($request)) {
            return redirect()->to(base_url('pusat_bantuan'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function isDeviceCompromised(RequestInterface $request): bool
    {
        $userAgent = $request->getHeaderLine('User-Agent');
        
        // Deteksi developer mode/emulator
        $developerModeIndicators = [
            'emulator',
            'simulator',
            'BlueStacks',
            'Nox',
            'MEmu',
            'LDPlayer',
            'Andy',
            'Genymotion',
        ];

        // Check Android
        if (strpos($userAgent, 'Android') !== false) {
            // Check emulator dari user-agent
            foreach ($developerModeIndicators as $indicator) {
                if (stripos($userAgent, $indicator) !== false) {
                    return true;
                }
            }

            // Deteksi dari JavaScript yang dikirim via request
            $jsPayload = $request->getPost('device_check');
            if ($jsPayload) {
                $decoded = json_decode($jsPayload, true);
                if (isset($decoded['is_emulator']) && $decoded['is_emulator']) {
                    return true;
                }
                if (isset($decoded['developer_mode']) && $decoded['developer_mode']) {
                    return true;
                }
            }
        }

        // Check iOS
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $jsPayload = $request->getPost('device_check');
            if ($jsPayload) {
                $decoded = json_decode($jsPayload, true);
                if (isset($decoded['developer_mode']) && $decoded['developer_mode']) {
                    return true;
                }
            }
        }

        return false;
    }
}
