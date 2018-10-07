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

  public static function getFieldValue($pData, $pFieldName) {

  }

  /**
   * Validate an associative array or an object 
   * 
   * $pConfig format : ['FieldName:ValidationType' => [Message, Operator/Pattern, CompareValue, CompareMaxValue], ... ]
   *
   * Validation types : required, regex, email, int, float, date
   * 
   * @param   array|object  $pData            Data to validate (object or associative array)
   * @param   array         $pConfig          Validation configuration
   */  
  public static function validate($pData, $pConfig) {
    // For each configuration value
    foreach ($pConfig as $ConfigKey => $ConfigValue) {
      // Try to determine the field to validate and the validation type
      $TabKey = Str::split($ConfigKey, ':');
      if(count($TabKey) == 1) {
        $TabKey[1] = 'required';  // required is the validation type by default
      }

      // Try to determine to the field value
      $FieldValue = null;
      if(is_array($pData) ? array_key_exists($TabKey[0], $pData) : false) {
        $FieldValue = $pData[$TabKey[0]];     // Array value
      }
      else if(is_object($pData) ? property_exists($pData, $TabKey[0]) : false) {
        $FieldValue = $pData->{$TabKey[0]};   // Object proprty
      }

      $ValidationResult = true;
      if($TabKey[1] == 'required') {  // Required field
        $ValidationResult = Validation::validateRequired($FieldValue);
      }
      else if($TabKey[1] == 'regex') {  // Regular expression
        $ValidationResult = Validation::validateRegex($FieldValue, $ConfigValue[1]);
      }
      else if($TabKey[1] == 'email') {  // Email address
        $ValidationResult = Validation::validateEmail($FieldValue);
      }
      else if($TabKey[1] == 'int' || $TabKey[1] == 'float' || $TabKey[1] == 'date') {   // Int, Float, Date
        $Operator = isset($ConfigValue[1]) ? $ConfigValue[1] : Validation::OPERATOR_TYPECHECK;
        $CompareValue = isset($ConfigValue[2]) ? $ConfigValue[2] : null;
        $CompareMaxValue = isset($ConfigValue[3]) ? $ConfigValue[3] : null;
        if($TabKey[1] == 'int') {  // Int number
          $ValidationResult = Validation::validateInt($FieldValue, $Operator, $CompareValue, $CompareMaxValue);
        }
        else if($TabKey[1] == 'float') {  // Float number
          $ValidationResult = Validation::validateFloat($FieldValue, $Operator, $CompareValue, $CompareMaxValue);
        }
        else if($TabKey[1] == 'date') {  // Date
          $ValidationResult = Validation::validateDate($FieldValue, $Operator, $CompareValue, $CompareMaxValue);
        }
      }

      // Check result
      if(!$ValidationResult) {
        throw new \Exception($ConfigValue[0]);
      }
    }
  }
}
