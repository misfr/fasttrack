<?php
/**
 * Data validation utility class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Models;

use FastTrack\ObjectBase;
use FastTrack\Str;
use FastTrack\Convert;

/**
 * Data validation utility class
 */
class Validation extends ObjectBase {
  /**
   * Comparison validation operator: data type check only
   * 
   * @var int
   */
  const OPERATOR_TYPECHECK = 0;
  
  /**
   * Comparison validation operator: Equal
   *
   * @var int
   */
  const OPERATOR_EQUAL = 1;
  
  /**
   * Comparison validation operator: Not equal
   *
   * @var int
   */
  const OPERATOR_NOTEQUAL = 2;
  
  /**
   * Comparison validation operator: Greater than
   *
   * @var int
   */
  const OPERATOR_GREATERTHAN = 3;
  
  /**
   * Comparison validation operator: Greater than or equal
   *
   * @var int
   */
  const OPERATOR_GREATERTHANEQUAL = 4;
  
  /**
   * Comparison validation operator: Less than
   *
   * @var int
   */
  const OPERATOR_LESSTHAN = 5;
  
  /**
   * Comparison validation operator: Less than or equal 
   *
   * @var int
   */
  const OPERATOR_LESSTHANEQUAL = 6;
  
  /**
   * Comparison validation operator: Range
   *
   * @var int
   */
  const OPERATOR_RANGE = 7;
  
  /**
   * Required field validation
   * 
   * @param   mixed   $pFieldValue    Field value to validate
   * @return  bool                    True on validation success, otherwise false
   */
  public static function validateRequired($pFieldValue) {
    if (Str::isNullOrEmpty($pFieldValue)) {
      return false;
    }
    return true;
  }

  /**
   * Regex validation
   * 
   * @param   mixed   $pFieldValue    Field value to validate
   * @param   string  $pRegexPattern  Pattern of the regular expression used to perform validation
   * @return  bool                    True on validation success, otherwise false
   */
  public static function validateRegex($pFieldValue, $pRegexPattern) {
    if (!Str::isNullOrEmpty($pFieldValue)) {
      if (!preg_match(Str::replace($pRegexPattern, '/', '\/'), $pFieldValue)) {
        return false;
      }
    }
    return true;
  }

  /**
   * Int number validation
   * 
   * @param   mixed   $pFieldValue            Field value to validate
   * @param   int     $pOperator              Comparison operator (const OPERATOR_xxx) 
   * @param   int     $pValueToCompare        Value to compare with
   * @param   int     $pMaxValueToCompare     Maximum valueto compare with
   * @return  bool                            True on validation success, otherwise false
   */
  public static function validateInt($pFieldValue, $pOperator = Validation::OPERATOR_TYPECHECK, $pValueToCompare = null, $pMaxValueToCompare = null) {
    if (!Str::isNullOrEmpty($pFieldValue)) {
      if (!preg_match('/^-?[0-9]+$/', $pFieldValue)) {
        return false;
      }
      $pFieldValue = intval($pFieldValue);

      // We must compare the value
      if ($pOperator != Validation::OPERATOR_TYPECHECK) {
        if ($pValueToCompare === null) {
          throw new \Exception("Value to compare must be set.");
        }
        
        switch ($pOperator) {
          case Validation::OPERATOR_EQUAL:
            return ($pFieldValue === $pValueToCompare);
          case Validation::OPERATOR_NOTEQUAL:
            return ($pFieldValue !== $pValueToCompare);
          case Validation::OPERATOR_GREATERTHAN:
            return ($pFieldValue > $pValueToCompare);
          case Validation::OPERATOR_LESSTHAN:
            return ($pFieldValue < $pValueToCompare);
          case Validation::OPERATOR_GREATERTHANEQUAL:
            return ($pFieldValue >= $pValueToCompare);
          case Validation::OPERATOR_LESSTHANEQUAL:
            return ($pFieldValue <= $pValueToCompare);
          case Validation::OPERATOR_RANGE:
            if ($pMaxValueToCompare === null) {
              throw new \Exception("Max value to compare must be set.");
            }
            return ($pFieldValue >= $pValueToCompare) && ($pFieldValue <= $pMaxValueToCompare);
        }
      }
    }

    return true;
  }

  /**
   * Float number validation
   *
   * @param   mixed   $pFieldValue            Field value to validate
   * @param   int     $pOperator              Comparison operator (const OPERATOR_xxx)
   * @param   float   $pValueToCompare        Value to compare with
   * @param   float   $pMaxValueToCompare     Maximum valueto compare with
   * @return  bool                            True on validation success, otherwise false
   */
  public static function validateFloat($pFieldValue, $pOperator = Validation::OPERATOR_TYPECHECK, $pValueToCompare = null, $pMaxValueToCompare = null) {
    if (!Str::isNullOrEmpty($pFieldValue)) {
      if (!preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $pFieldValue)) {
        return false;
      }
      $pFieldValue = floatval($pFieldValue);
      
      // We must compare the value
      if ($pOperator != Validation::OPERATOR_TYPECHECK) {
        if ($pValueToCompare === null) {
          throw new \Exception("Value to compare must be set.");
        }
        
        switch ($pOperator) {
          case Validation::OPERATOR_EQUAL:
            return ($pFieldValue === $pValueToCompare);
          case Validation::OPERATOR_NOTEQUAL:
            return ($pFieldValue !== $pValueToCompare);
          case Validation::OPERATOR_GREATERTHAN:
            return ($pFieldValue > $pValueToCompare);
          case Validation::OPERATOR_LESSTHAN:
            return ($pFieldValue < $pValueToCompare);
          case Validation::OPERATOR_GREATERTHANEQUAL:
            return ($pFieldValue >= $pValueToCompare);
          case Validation::OPERATOR_LESSTHANEQUAL:
            return ($pFieldValue <= $pValueToCompare);
          case Validation::OPERATOR_RANGE:
            if ($pMaxValueToCompare === null) {
              throw new \Exception("Max value to compare must be set.");
            }
            return ($pFieldValue >= $pValueToCompare) && ($pFieldValue <= $pMaxValueToCompare);
        }
      }
    }
    
    return true;
  }
  
  /**
   * Date validation
   *
   * @param   mixed       $pFieldValue            Field value to validate
   * @param   int         $pOperator              Comparison operator (const OPERATOR_xxx)
   * @param   \DateTime   $pValueToCompare        Value to compare with
   * @param   \DateTime   $pMaxValueToCompare     Maximum valueto compare with
   * @return  bool                                True on validation success, otherwise false
   */
  public static function validateDate($pFieldValue, $pOperator = Validation::OPERATOR_TYPECHECK, $pValueToCompare = null, $pMaxValueToCompare = null) {
    if (!Str::isNullOrEmpty($pFieldValue)) {
      if (!Convert::tryParseDateTime($pFieldValue, $pFieldValue)) {
          return false;
      }
      
      // We must compare the value
      if ($pOperator != Validation::OPERATOR_TYPECHECK) {
        if ($pValueToCompare === null) {
          throw new \Exception("Value to compare must be set.");
        }
        
        switch ($pOperator) {
          case Validation::OPERATOR_EQUAL:
            return ($pFieldValue === $pValueToCompare);
          case Validation::OPERATOR_NOTEQUAL:
            return ($pFieldValue !== $pValueToCompare);
          case Validation::OPERATOR_GREATERTHAN:
            return ($pFieldValue > $pValueToCompare);
          case Validation::OPERATOR_LESSTHAN:
            return ($pFieldValue < $pValueToCompare);
          case Validation::OPERATOR_GREATERTHANEQUAL:
            return ($pFieldValue >= $pValueToCompare);
          case Validation::OPERATOR_LESSTHANEQUAL:
            return ($pFieldValue <= $pValueToCompare);
          case Validation::OPERATOR_RANGE:
            if ($pMaxValueToCompare === null) {
              throw new \Exception("Max value to compare must be set.");
            }
            return ($pFieldValue >= $pValueToCompare) && ($pFieldValue <= $pMaxValueToCompare);
        }
      }
    }
    
    return true;
  }
  
  /**
   * Email address validation
   *
   * @param   mixed   $pFieldValue    Field value to validate
   * @return  bool                    True on validation success, otherwise false
   */
  public static function validateEmail($pFieldValue) {
    if (!Str::isNullOrEmpty($pFieldValue)) {
      if (!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/',  $pFieldValue)) {
        return false;
      }
    }
    return true;
  }
}
