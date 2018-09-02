<?php
/**
 * Handle MySQL databases queries
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data\MySql;

use FastTrack\Data\DbCommand;
use FastTrack\Data\DbParameter;
use FastTrack\Convert;
use FastTrack\Str;

/**
 * Handle MySQL databases queries
 */
class MySqlCommand extends DbCommand {
  /**
   * PDO object used for preparing and executing queries
   *
   * @var \PDOStatement
   */
  protected $_Statement;

  /**
   * Execute one or more queries without returning result
   * 
   * @throws \Exception
   */
  public function executeNonQuery() {
    parent::executeNonQuery();

    /** @var \PDO $PDOObject */
    $PDOObject = $this->_Cnx->CnxObject;
    $this->_Statement = $PDOObject->prepare($this->CommandText);

    // Add parameters
    foreach($this->_Parameters as $ParamName => $ParamItem) {
      /** @var DbParameter $ParamItem */

      // Try to determine parameter type
      $ParamType = \PDO::PARAM_STR;
      switch($ParamItem->Type) {
        case DbParameter::TYPE_BOOL:
          $ParamType = \PDO::PARAM_BOOL;
          if($ParamItem->Value !== null && !Convert::tryParseBool($ParamItem->Value, $ParamItem->Value)) {
            throw new \Exception("Unable to convert the parameter $ParamName to a boolean.");
          }
          break;
        case DbParameter::TYPE_INT:
          $ParamType = \PDO::PARAM_INT;
          if($ParamItem->Value !== null && !Convert::tryParseInt($ParamItem->Value, $ParamItem->Value)) {
            throw new \Exception("Unable to convert the parameter $ParamName to an integer.");
          }
          break;
        case DbParameter::TYPE_FLOAT:
          $ParamType = \PDO::PARAM_STR;
          if($ParamItem->Value !== null && !Convert::tryParseFloat($ParamItem->Value, $ParamItem->Value)) {
            throw new \Exception("Unable to convert the parameter $ParamName to a float number.");
          }
          break;
        case DbParameter::TYPE_DATETIME:
          if($ParamItem->Value !== null) {
            if(is_object($ParamItem->Value) ? !($ParamItem->Value instanceof \DateTime) : true) {
              throw new \Exception("The parameter $ParamName must be an instance of DateTime.");
            }
            $ParamItem->Value = $ParamItem->Value->format($this->_Cnx->DateTimeFormat);
          }
          $ParamType = \PDO::PARAM_STR;
          break;
        case DbParameter::TYPE_STRING:
          // If the value of the parameter is an empty string and we must convert it to null
          if(Str::isNullOrEmpty($ParamItem->Value) && $ParamItem->EmptyStringToNull == true) {
            $ParamItem->Value = null;
          }
          break;
      }

      // Try to determine the direction of the parameter
      if($ParamItem->Direction == DbParameter::DIRECTION_INPUTOUTPUT) {
        $ParamType = $ParamType | \PDO::PARAM_INPUT_OUTPUT;
      }

      // Add the parameter
      $this->_Statement->bindParam($ParamName, $ParamItem->Value, $ParamType, $ParamItem->Length);
    }

    // Execute the query
    if($this->_Statement->execute() == false) {
      throw new \Exception($this->_Statement->errorInfo()[2]);
    }
  }

  /**
   * Execute one or more queries and returns the first available result
   * 
   * @param   \Closure|null    $pStrongTypeCallBack     Callback function to call to string type the result
   *    ex: function(i) { return boolval(i); }
   * @return  mixed                                     Value of the first column of the first returned line
   */
  public function executeScalar($pStrongTypeCallBack = null) {
    $ReturnValue = null;
    $this->executeNonQuery();
    $Result = null;

    // Last result set
    do {
      if($this->_Statement->columnCount() > 0) {
        $Result = $this->fetchResults();
      }
    } while($this->_Statement->nextRowset());

    if($Result !== null ? count($Result) > 0 : false) {
      foreach($Result[0] as $ResultCol) {
        // First column of the first returned result
        $ReturnValue = $ResultCol;
        break;
      }
    }

    // Strong type the result
    if($pStrongTypeCallBack !== null) {
      $ReturnValue = $pStrongTypeCallBack($ReturnValue);
    }

    return $ReturnValue;
  }

  /**
   * Execute one or more queries and returns all results of the last query
   * 
   * @param   \Closure[]|null  $pStrongTypeCallBacks    Associative array of callback functions to call to string type the result.
   *   ex: ["KeyToStringType" => function(i) { return boolval(i); }]
   * @return  array                                     Results of the last query
   */
  public function executeReader($pStrongTypeCallBacks = null) {
    $ReturnValue = null;
    $this->executeNonQuery();

    // Last result set
    do {
      if($this->_Statement->columnCount() > 0) {
        $ReturnValue = $this->fetchResults();
      }
    } while($this->_Statement->nextRowset());

    // Strong type the results
    if($pStrongTypeCallBacks !== null) {
      // For each row
      for($i = 0; $i < count($ReturnValue); $i++) {
        // For each callback
        foreach ($pStrongTypeCallBacks as $Key => $TypeCallBack) {
          $ReturnValue[$i][$Key] = $TypeCallBack($ReturnValue[$i][$Key]);
        }
      }
    }

    return $ReturnValue;
  }

  /**
   * Get a results set with automatic tye conversion
   *
   * @return array Results set
   */
  protected function fetchResults() {
    return $this->_Statement->fetchAll(\PDO::FETCH_ASSOC);
  }
}
