<?php

namespace App\Libraries;

class AuditorAwareImpl implements AuditorAwareInterface
{
  private static ?string $forcedAuditor = null;

  public static function setAuditor(?string $auditor): void
  {
    self::$forcedAuditor = $auditor;
  }

  public function getCurrentAuditor(): string
  {
    if (self::$forcedAuditor !== null && self::$forcedAuditor !== '') {
      return self::$forcedAuditor;
    }

    $jwtAuditor = $this->getAuditorFromJwtHeader();
    if ($jwtAuditor !== null && $jwtAuditor !== '') {
      return $jwtAuditor;
    }

    $sessionAuditor = $this->getAuditorFromSession();
    if ($sessionAuditor !== null && $sessionAuditor !== '') {
      return $sessionAuditor;
    }

    return 'SYSTEM';
  }

  protected function getAuditorFromJwtHeader(): ?string
  {
    try {
      if (function_exists('service')) {
        $request = service('request');
        if ($request && method_exists($request, 'getHeaderLine')) {
          $authHeader = $request->getHeaderLine('Authorization');
          if (!empty($authHeader) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return $this->extractUsernameFromJwt($matches[1]);
          }
        }
      }
    } catch (\Throwable $e) {
    }

    return null;
  }

  protected function getAuditorFromSession(): ?string
  {
    try {
      if (function_exists('session')) {
        $session = session();
        if ($session) {
          $username = $session->get('username')
            ?? $session->get('employee_no')
            ?? $session->get('name');

          if (!empty($username)) {
            return (string) $username;
          }

          $accessToken = $session->get('access_token');
          if (!empty($accessToken)) {
            $jwtAuditor = $this->extractUsernameFromJwt((string) $accessToken);
            if (!empty($jwtAuditor)) {
              return $jwtAuditor;
            }
          }
        }
      }
    } catch (\Throwable $e) {
    }

    return null;
  }

  public function extractUsernameFromJwt(string $jwt): ?string
  {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
      return null;
    }

    $payloadJson = base64_decode(strtr($parts[1], '-_', '+/'));
    if (!$payloadJson) {
      return null;
    }

    $payload = json_decode($payloadJson, true);
    if (!is_array($payload)) {
      return null;
    }

    return $payload['username']
      ?? $payload['preferred_username']
      ?? $payload['name']
      ?? $payload['sub']
      ?? null;
  }
}
