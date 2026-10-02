<?php

namespace App\Services;

use Config\Integration;
use CodeIgniter\HTTP\CURLRequest;
use Config\Services;
use Throwable;

class SpringHelpdeskService
{
    private Integration $config;

    public function __construct()
    {
        $this->config = config(Integration::class) ?? new Integration();
    }

    private function getClient(): CURLRequest
    {
        return Services::curlrequest([
            'timeout'     => 10,
            'http_errors' => false,
            'verify'      => false,
        ]);
    }

    /**
     * Hit Spring Boot GET /api/helpdesk/user?employeeNo=... dengan Basic Auth
     */
    public function getUserByEmployeeNo(string $employeeNo): ?array
    {
        // Pakai 127.0.0.1 jika localhost bermasalah dengan IPv6 di Windows
        $baseUrl = rtrim($this->config->helpdeskApiBaseUrl, '/');
        $baseUrl = str_replace('localhost', '127.0.0.1', $baseUrl);

        $url = $baseUrl . '/api/helpdesk/user';

        try {
            $response = $this->getClient()->get($url, [
                'auth' => [
                    $this->config->helpdeskApiUser,
                    $this->config->helpdeskApiPass,
                    'basic'
                ],
                'query' => [
                    'employeeNo' => $employeeNo
                ],
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode === 200) {
                return json_decode($body, true);
            }

            log_message('error', "Spring Boot responded [{$statusCode}]: {$body}");
            return null;

        } catch (Throwable $e) {
            // Tangkap exception cURL agar tidak melempar halaman merah CI4
            log_message('error', "cURL Connection Failed to {$url}: " . $e->getMessage());
            error_log("cURL Connection Failed to {$url}: " . $e->getMessage());
            return null;
        }
    }
}