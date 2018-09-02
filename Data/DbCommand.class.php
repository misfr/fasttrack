<?php
/**
 * Handle database queries
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data;

use FastTrack\ObjectBase;
use FastTrack\Str;

/**
 * Handle database queries
 *
 * @property-read   DbParameterCollection   $Parameters     Queries parameters
 */
class DbCommand extends ObjectBase {
  /**
   * Database connection
   * 
   * @var DbConnection
   */
  protected $_Cnx;

  /**
   * One or more queries to execute
   * 
   * @var string
   */
  public $CommandText;

  /**
   * Queries parameters collection
   * 
   * @return DbParameterCollection
   */
  protected function &getParameters() {
    return $this->_Parameters;
  }
  /**
   * Queries parameters collection
   * 
   * @var DbParameterCollection
   */
  protected $_Parameters;

  /**
   * Class constructor
   *
   * @param DbConnection $pCnx Database connection
   */
  public function __construct($pCnx) {
    // Initializations
    $this->_Cnx = $pCnx;
    $this->_Parameters = new DbParameterCollection();
  }

  /**
   * Execute one or more queries that returns nothing
   * 
   * @throws \Exception
   */
  public function executeNonQuery() {
    if(Str::isNullOrWhiteSpace($this->CommandText)) {
      throw new \Exception('Query is empty.');
    }
    // Must be redefined for the targeted database engine
  }

  /**
   * Execute one or more queries and returns the first available result
   * 
   * @param   \Closure    $pStrongTypeCallBack    Callback function to call to string type the result
   *    ex: function(i) { return boolval(i); }
   * @return  mixed                               Value of the first column of the first returned line
   * @throws  \Exception
   */
  public function executeScalar($pStrongTypeCallBack) {
    if(Str::isNullOrWhiteSpace($this->CommandText)) {
      throw new \Exception('Query is empty.');
    }

    // Must be redefined for the targeted database engine
    return null;
  }

  /**
   * Execute one or more queries and returns all results of the last query
   * 
   * @param   \Closure[]|null  $pStrongTypeCallBacks    Associative array of callback functions to call to string type the result.
   *   ex: ["KeyToStringType" => function(i) { return boolval(i); }]
   * @return  array                                     Results of the last query
   * @throws  \Exception
   */
  public function executeReader($pStrongTypeCallBacks = null) {
    if(Str::isNullOrWhiteSpace($this->CommandText)) {
      throw new \Exception('Query is empty.');
    }

    // Must be redefined for the targeted database engine
    return [];
  }
}
