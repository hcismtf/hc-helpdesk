<?php

namespace App\Libraries;

interface AuditorAwareInterface
{
    public function getCurrentAuditor(): string;
}
