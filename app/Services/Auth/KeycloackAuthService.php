<?php

namespace App\Services\Auth;

use Config\Integration;
use Config\Services;

class KeycloackAuthService
{
  protected Integration $config;
  protected $client;

  public function __construct()
  {
    $this->config = config('Integration');
    $this->client = Services::curlrequest([
      'timeout' => 10,
      'http_errors' => false,
    ]);
  }

  /**
   * Hit token Keycloak via Direct Access Grant (Password)
   */
  public function authenticate(string $username, string $password): ?array
  {
    $payload = [
      'grant_type' => 'password',
      'client_id' => $this->config->keycloakClientId,
      'username' => $username,
      'password' => $password,
    ];

    if (!empty($this->config->keycloakClientSecret)) {
      $payload['client_secret'] = $this->config->keycloakClientSecret;
    }

    $response = $this->client->post($this->config->keycloakTokenUrl, [
      'form_params' => $payload,
    ]);

    if ($response->getStatusCode() !== 200) {
      log_message('error', 'Keycloak auth failed: ' . $response->getBody());
      return null;
    }

    return json_decode($response->getBody(), true);
  }

  /**
   * Ambil data payload dari access_token JWT
   */
  public function parseTokenPayload(string $jwtToken): ?array
  {
    $parts = explode('.', $jwtToken);
    if (count($parts) !== 3) {
      return null;
    }

    $payload = base64_decode(strtr($parts[1], '-_', '+/'));
    return json_decode($payload, true);
  }

  /**
   * Hit Spring Boot /api/helpdesk/user dengan Bearer Token
   */
  public function getHelpdeskUserData(string $employeeNo): ?array
  {
    $url = rtrim($this->config->helpdeskApiBaseUrl, '/') . '/api/helpdesk/user';

    $options = [
      'auth' => [
        $this->config->helpdeskApiUser,
        $this->config->helpdeskApiPass,
        'basic'
      ],
      'headers' => [
        'Accept' => 'application/json',
      ],
      'query' => [
        'employeeNo' => $employeeNo,
      ],
    ];

    try {
      $response = $this->client->get($url, $options);
      $statusCode = $response->getStatusCode();
      $body = $response->getBody();

      if ($statusCode !== 200) {
        log_message('error', "Spring Boot error [HTTP {$statusCode}] URL: {$url} Body: {$body}");
        return null;
      }

      return json_decode($body, true);
    } catch (\Throwable $e) {
      log_message('error', "Spring Boot connection exception: " . $e->getMessage());
      return null;
    }
  }
}