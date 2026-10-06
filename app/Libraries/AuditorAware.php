<?php

namespace App\Libraries;

class AuditorAware
{
  private static ?AuditorAwareInterface $implementation = null;

  public static function getImplementation(): AuditorAwareInterface
  {
    if (self::$implementation === null) {
      self::$implementation = new AuditorAwareImpl();
    }
    return self::$implementation;
  }

  public static function setImplementation(AuditorAwareInterface $implementation): void
  {
    self::$implementation = $implementation;
  }

  public static function getCurrentAuditor(): string
  {
    return self::getImplementation()->getCurrentAuditor();
  }

  public static function setAuditor(?string $auditor): void
  {
    AuditorAwareImpl::setAuditor($auditor);
  }
}
