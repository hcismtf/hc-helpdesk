<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Integration extends BaseConfig
{
    // Keycloak
    public string $keycloakTokenUrl = 'http://localhost:8080/realms/mtf/protocol/openid-connect/token';
    public string $keycloakClientId = 'mtf-crud'; // sesuaikan clientId keycloak
    public string $keycloakClientSecret = 'rTbXoP5dFw7Lkcoc3ZgH3LRNTIjghhsn';       // jika client berstatus confidential

    // Spring Boot Helpdesk Endpoint
    public string $helpdeskApiBaseUrl   = 'http://localhost:9092'; 
    public string $helpdeskApiUser    = 'helpdesk';
    public string $helpdeskApiPass    = 'H3lpD$sk';
}