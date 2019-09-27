<?php
/**
 * Database query parameter
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data;

use FastTrack\ObjectBase;

/**
 * Database query parameter
 */
class DbParameter extends ObjectBase {
  /**
   * Parameter direction: Input only
   * 
   * @var int
   */
  const DIRECTION_INPUT = 0;

  /**
   * Parameter direction: Input/Output
   * 
   * @var int
   */
  const DIRECTION_INPUTOUTPUT = 1;
  
  /**
   * Parameter data type: string
   *
   * @var int
   */
  const TYPE_STRING = 0;
  
  /**
   * Parameter data type: boolean
   *
   * @var int
   */
  const TYPE_BOOL = 1;
  
  /**
   * Parameter data type: integer
   *
   * @var int
   */
  const TYPE_INT = 2;
  
  /**
   * Parameter data type: float number
   *
   * @var int
   */
  const TYPE_FLOAT = 3;
  
  /**
   * Parameter data type: DateTime
   *
   * @var int
   */
  const TYPE_DATETIME = 4;
  
  /**
   * Flag that indicates whether we must convert empty string to null
   * 
   * @var bool
   */
  public $EmptyStringToNull;

  /**
   * Direction of the parameter (const DIRECTION_xxx)
   * 
   * @var int
   */
  public $Direction;

  /**
   * Length of the parameter (string only)
   * 
   * @var int
   */
  public $Length;

  /**
   * Float numbers precision (6 by defaut)
   * 
   * @var int
   */
  public $FloatPrecision;

  /**
   * Type of the parameter (const TYPE_xxx)
   * 
   * @var int
   */
  public $Type;

  /**
   * Value of the parameter
   * 
   * @var mixed
   */
  public $Value;

  /**
   * Class constructor
   */
  public function __construct() {
    // Initializations
    $this->EmptyStringToNull = true;
    $this->Direction = DbParameter::DIRECTION_INPUT;
    $this->Length = 0;
    $this->FloatPrecision = 6;
    $this->Type = DbParameter::TYPE_STRING;
    $this->Value = '';
  }
}
