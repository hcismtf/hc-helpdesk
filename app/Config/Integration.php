<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Integration extends BaseConfig
{
  public string $keycloakTokenUrl     = '';
  public string $keycloakClientId     = '';
  public string $keycloakClientSecret = '';
  public string $helpdeskApiBaseUrl   = '';
  public string $helpdeskApiUser      = '';
  public string $helpdeskApiPass      = '';
}