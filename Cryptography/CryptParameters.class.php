<?php
/**
 * FastTrack cryptography parameters
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Cryptography;

use FastTrack\ObjectBase;
use FastTrack\Config;

/**
 * FastTrack cryptography parameters
 */
class CryptParameters extends ObjectBase {
  /**
   * Key
   * 
   * @var string
   */
  public $PassPhrase = '';

  /**
   * Salt key
   * 
   * @var string
   */
  public $Salt = '';

  /**
   * Initialisation vector
   * 
   * @var string
   */
  public $InitVector = '';

  /**
   * Gets the defaults crypt parameters (stored in the ~/app/config module)
   *
   * @return CryptParameters
   */
  public static function getDefaults() {
    $ReturnValue = new CryptParameters();
    $ReturnValue->PassPhrase = Config::$CryptPassPhrase;
    $ReturnValue->Salt = Config::$CryptSalt;
    $ReturnValue->InitVector = Config::$CryptInitVector;
    return $ReturnValue;
  }
}
