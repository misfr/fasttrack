<?php
/**
 * Database query parameters collection
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data;

/**
 * Database query parameters collection
 */
class DbParameterCollection extends \ArrayObject {
  /**
   * Add a query parameter to the collection
   *
   * @param string    $pName                  Name of the parameter
   * @param int       $pType                  Type of the parameter (const DbParameter::TYPE_xxx)
   * @param int       $pDirection             Direction of the parameter (const DbParameter::DIRECTION_xxx)
   * @param int       $pLength                Length of the parameter (required for input/output parameters)
   * @param bool      $pEmptyStringToNull     Flag that indicates whether we shoud convert empty strings to null
   */
  public function addParam($pName, $pType = DbParameter::TYPE_STRING, $pDirection = DbParameter::DIRECTION_INPUT, $pLength = 0, $pEmptyStringToNull = true) {
    $NewParameter = new DbParameter();
    $NewParameter->Value = null;
    $NewParameter->Type = $pType;
    $NewParameter->Direction = $pDirection;
    $NewParameter->Length = $pLength;
    $NewParameter->EmptyStringToNull = $pEmptyStringToNull;
    $this->offsetSet($pName, $NewParameter);
  }

  /**
   * Add a query parameter with its value to the collection
   *
   * @param string    $pName                  Name of the parameter
   * @param int       $pType                  Type of the parameter (const DbParameter::TYPE_xxx)
   * @param int       $pDirection             Direction of the parameter (const DbParameter::DIRECTION_xxx)
   * @param int       $pDirection             Direction of the parameter (const of the Blocks\Data\DbParameterDirection class)
   * @param int       $pLength                Length of the parameter (required for input/output parameters)
   * @param bool      $pEmptyStringToNull     Flag that indicates whether we shoud convert empty strings to null
   */
  public function addParamWithValue($pName, $pValue, $pType = DbParameter::TYPE_STRING, $pDirection = DbParameter::DIRECTION_INPUT, $pLength = 0, $pEmptyStringToNull = true) {
    $NewParameter = new DbParameter();
    $NewParameter->Value = $pValue;
    $NewParameter->Type = $pType;
    $NewParameter->Direction = $pDirection;
    $NewParameter->Length = $pLength;
    $NewParameter->EmptyStringToNull = $pEmptyStringToNull;
    $this->offsetSet($pName, $NewParameter);
  }
}
