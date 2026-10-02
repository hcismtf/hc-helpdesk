<?php

namespace App\Services;

use Config\Integration;
use CodeIgniter\HTTP\CURLRequest;

class KeycloakService
{
    private Integration $config;
    private CURLRequest $client;

    public function __construct()
    {
        $this->config = config('Integration');
        $this->client = \Config\Services::curlrequest([
            'timeout' => 10,
            'http_errors' => false
        ]);
    }

    /**
     * Authenticate credential via Resource Owner Password Credentials Grant
     */
    public function authenticate(string $username, string $password): ?array
    {
        $payload = [
            'grant_type' => 'password',
            'client_id'  => $this->config->keycloakClientId,
            'username'   => $username,
            'password'   => $password,
            'scope'      => 'openid',
        ];

        if (!empty($this->config->keycloakClientSecret)) {
            $payload['client_secret'] = $this->config->keycloakClientSecret;
        }

        $response = $this->client->post($this->config->keycloakTokenUrl, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => $payload
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody();

        log_message('error', "Keycloak Response [{$statusCode}]: " . $body);

        if ($statusCode === 200) {
          return json_decode($body, true);
        }

        log_message('warning', 'Keycloak auth failed: ' . $response->getBody());
        return null;
    }

    /**
     * Decode JWT token payload tanpa verify external cert (untuk ambil claims/employeeNo)
     */
    public function parseTokenPayload(string $accessToken): ?array
    {
        $parts = explode('.', $accessToken);
        if (count($parts) !== 3) {
            return null;
        }
        return json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    }
}