<?php
/**
 * Localization classes
 *
 * @author Frederic BAYLE
 */

namespace FastTrack;

/**
 * Localization data class
 */
class LocalizationData extends ObjectBase {
  /**
   * Locale name
   * 
   * @var string
   */
  public $Name = 'en';
  
  /**
   * DateTime format
   * 
   * @var string
   */
  public $DateFormat = '/^(?<month>[0-9]{1,2})\/(?<day>[0-9]{1,2})\/(?<year>[0-9]{4,})(?:\s+(?<hour>[0-9]{1,2}):(?<minutes>[0-9]{1,2})(?::(?<seconds>[0-9]{1,2}))?)?/';
  
  /**
   * Decimal separator
   * 
   * @var string
   */
  public $DecimalSeparator = '.';
}
    
/**
 * Localization class
 */
class Localization extends ObjectBase {
  /**
   * Current Locale
   * 
   * @var LocalizationData
   */
  public static $CurrentLocale = null;
  
  /**
   * Set the current application locale to use
   * 
   * @param string $pLocale Current application locale to use
   */
  public static function setCurrentLocale($pLocale) {
    $LocaleFileName = __DIR__ . '/L10n/FastTrack.l10n.' . $pLocale . '.php';
    if(!file_exists($LocaleFileName)) {
      throw new \Exception("Unable to load the locale from $LocaleFileName.");
    }
    Localization::$CurrentLocale = include($LocaleFileName);
  }
}

// Create the default locale (en)
Localization::$CurrentLocale = new LocalizationData();
