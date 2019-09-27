<?php
/**
 * Type conversion utility class
 *
 * @author  Frederic BAYLE
 */

namespace FastTrack;

/**
 * Type conversion utility class
 */
class Convert extends ObjectBase {
  /**
   * Base64 convert mode: Standard
   *
   * @var int
   */
  const BASE64MODE_STANDARD = 0;
  
  /**
   * Base64 convert mode: URL
   *
   * @var int
   */
  const BASE64MODE_URL = 1;
  
  /**
   * Decode a string from the Base64 format
   *
   * @param   string  $pInput     String to decode
   * @param   int     $pMode      Encoding mode
   * @return  string              Decoded string
   */
  public static function fromBase64String($pInput, $pMode = Convert::BASE64MODE_STANDARD) {
    $ReturnValue = $pInput;

    switch($pMode) {
      case Convert::BASE64MODE_URL: // URL mode
        $ReturnValue = Str::replace($ReturnValue, '-', '+');
        $ReturnValue = Str::replace($ReturnValue, '_', '/');
        $ReturnValue = Str::replace($ReturnValue, '.', '=');
        break;
      case Convert::BASE64MODE_STANDARD: // Standard mode
      default:
        break;
    }

    return base64_decode($ReturnValue);
  }

  /**
   * Encode a string into the Base64 format
   *
   * @param   string  $pInput     String to encode
   * @param   int     $pMode      Encoding mode (const from the Blocks\Base64Mode class)
   * @return  string              Base64 encoded string
   */
  public static function toBase64String($pInput, $pMode = Convert::BASE64MODE_STANDARD) {
    $ReturnValue = base64_encode($pInput);

    switch($pMode) {
      case Convert::BASE64MODE_URL: // URL mode
        $ReturnValue = Str::replace($ReturnValue, '+', '-');
        $ReturnValue = Str::replace($ReturnValue, '/', '_');
        $ReturnValue = Str::replace($ReturnValue, '=', '.');
        break;
      case Convert::BASE64MODE_STANDARD: // Standard mode
      default:
        break;
    }

    return $ReturnValue;
  }

  /**
   * Convert a value to a boolean
   *
   * @param   mixed       $pInputValue    Value to convert
   * @return  bool
   * @throws  \Exception
   */
  public static function toBool($pInputValue) {
    $ReturnValue = $pInputValue;

    if(is_string($ReturnValue)) {
      if(Str::lower($ReturnValue) == 'false' || $ReturnValue == '0') {
        $ReturnValue = false;
      } else if(Str::lower($ReturnValue) == 'true' || $ReturnValue == '1') {
        $ReturnValue = true;
      } else {
          throw new \Exception("Can't convert $ReturnValue to a boolean.");
      }
    } else if(is_numeric($ReturnValue)) {
      $ReturnValue = $ReturnValue != 0;
    } else if(!is_bool($ReturnValue)) {
      throw new \Exception("Can't convert $ReturnValue to a boolean.");
    }

    return boolval($ReturnValue);
  }

  /**
   * Convert a value to a DateTime object
   *
   * @param   mixed       $pInputValue    Value to convert
   * @return  \DateTime
   * @throws  \Exception
   */
  public static function toDateTime($pInputValue) {
    $ReturnValue = null;
    if($pInputValue instanceof \DateTime) {
      return $pInputValue;
    }
    else if (is_object($pInputValue)) {
      throw new \Exception("Can't convert " . print_r($pInputValue, true) . " to DateTime.");
    }
    else if(!Convert::tryParseDateTime($pInputValue, $ReturnValue)) {
      throw new \Exception("Can't convert " . print_r($pInputValue, true) . " to DateTime.");
    }

    return $ReturnValue;
  }

  /**
   * Convert a value to a float number
   *
   * @param   mixed       $pInputValue    Value to convert
   * @return  float
   * @throws  \Exception
   */
  public static function toFloat($pInputValue) {
    $ReturnValue = Str::replace($pInputValue, Localization::$CurrentLocale->DecimalSeparator, '.');
    if(!preg_match('/^-?[0-9]+(\.[0-9]+)?(e-?[0-9]+)?$/i', $ReturnValue)) {
      throw new \Exception("Can't convert $ReturnValue to a float number.");
    }
    return floatval($ReturnValue);
  }

  /**
   * Convert a value to an integer
   *
   * @param   mixed       $pInputValue    Value to convert
   * @return  int
   * @throws  \Exception
   */
  public static function toInt($pInputValue) {
    $ReturnValue = $pInputValue;
    if(!preg_match('/^-?[0-9]+$/', $ReturnValue)) {
      throw new \Exception("Can't convert $ReturnValue to an integer.");
    }
    return intval($ReturnValue);
  }

  /**
   * Try to convert a value to a boolean
   *
   * @param   mixed   $pInputValue    Value to convert
   * @param   bool    $pOutputValue   Converted value
   * @return  bool                    Flag that determines whether the value was successfully converted
   */
  public static function tryParseBool($pInputValue, &$pOutputValue) {
    $ReturnValue = true;
    try {
      $pOutputValue = Convert::toBool($pInputValue);
    } catch(\Exception $e) {
      $ReturnValue = false;
    }
    return $ReturnValue;
  }

  /**
   * Try to convert a value to a DateTime object
   *
   * @param   mixed       $pInputValue    Value to convert
   * @param   \DateTime   $pOutputValue   Result
   * @return  bool                        Flag that determines whether the value was successfully converted
   */
  public static function tryParseDateTime($pInputValue, &$pOutputValue) {
    $pOutputValue = new \DateTime();
    $NbDaysMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    // Check date and time pattern test with the international notation yyyy-mm-dd hh:mm:ss
    $RxResult = null;
    if(!preg_match('/^(?<year>[0-9]{4,})-(?<month>[0-9]{1,2})-(?<day>[0-9]{1,2})(?:\s+(?<hour>[0-9]{1,2}):(?<minutes>[0-9]{1,2})(?::(?<seconds>[0-9]{1,2}))?)?/', $pInputValue, $RxResult)) {
      if(!preg_match(Localization::$CurrentLocale->DateFormat, $pInputValue, $RxResult)) {
        return false;
      }
    }

    // Extract day, month, year, hour, minutes and seconds
    $Day = intval($RxResult['day']);
    $Month = intval($RxResult['month']);
    $Year = intval($RxResult['year']);
    $Hour = isset($RxResult['hour']) ? intval($RxResult['hour']) : 0;
    $Minutes = isset($RxResult['minutes']) ? intval($RxResult['minutes']) : 0;
    $Seconds = isset($RxResult['seconds']) ? intval($RxResult['seconds']) : 0;

    // Adjust february on leap years
    if($Year % 4 == 0) {
      $NbDaysMonth[1] = 29;
    }

    // Check values
    if($Month < 1 || $Month > 12 || $Day < 1 || $Day > $NbDaysMonth[$Month - 1]) {
      return false;
    }
    if($Hour < 0 || $Hour > 23 || $Minutes < 0 || $Minutes > 59 || $Seconds < 0 || $Seconds > 59) {
      return false;
    }

    // Build a DateTime object from values
    $pOutputValue->setDate($Year, $Month, $Day);
    $pOutputValue->setTime($Hour, $Minutes, $Seconds);
    return true;
  }

  /**
   * Try to convert a value to a float number
   *
   * @param   mixed   $pInputValue    Value to convert
   * @param   float   $pOutputValue   Converted value
   * @return  bool                    Flag that determines whether the value was successfully converted
   */
  public static function tryParseFloat($pInputValue, &$pOutputValue) {
    $ReturnValue = true;
    try {
      $pOutputValue = Convert::toFloat($pInputValue);
    } catch(\Exception $e) {
      $ReturnValue = false;
    }
    return $ReturnValue;
  }

  /**
   * Try to convert a value to an integer
   *
   * @param   mixed   $pInputValue    Value to convert
   * @param   int     $pOutputValue   Converted value
   * @return  bool                    Flag that determines whether the value was successfully converted
   */
  public static function tryParseInt($pInputValue, &$pOutputValue) {
    $ReturnValue = true;
    try {
      $pOutputValue = Convert::toInt($pInputValue);
    } catch(\Exception $e) {
      $ReturnValue = false;
    }
    return $ReturnValue;
  }
}
	