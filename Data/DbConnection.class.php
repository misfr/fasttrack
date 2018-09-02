<?php
/**
 * Database connection
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data;

use FastTrack\ObjectBase;

/**
 * Database connection
 *
 * @property-read string $CnxObject         Database connection object (such as PDO for example)
 * @property-read string $ConnectionString  Database connection string
 */
class DbConnection extends ObjectBase {
  /**
   * Database connection object (such as PDO for example)
   * 
   * @return mixed
   */
  protected function &getCnxObject() {
      return $this->_CnxObject;
  }
  /**
   * Database connection object (such as PDO for example)
   * 
   * @var mixed
   */
  protected $_CnxObject;

  /**
   * Database connection pool
   *
   * <p>
   * New databases connections will be cached in this pool.
   * If an incomming database connection has been established before,
   * the connection will be restored from this pool. It prevents multiple connections and optimizes performances
   * </p>
   *
   * @var array
   */
  public static $ConnectionPool = [];

  /**
   * Database connection string
   * 
   * @return string
   */
  protected function &getConnectionString() {
    return $this->_ConnectionString;
  }
  /**
   * Database connection string
   *
   * @var string
   */
  protected $_ConnectionString;

  /**
   * DateTime format for this database connexion
   * 
   * @var string
   */
  public $DateTimeFormat = 'Y-m-d H:i:s';
  
  /**
   * Class constructor
   *
   * @param string $pConnectionString Database connection string (used in _CnxObject)
   */
  public function __construct($pConnectionString) {
    // Initializations
    $this->_ConnectionString = $pConnectionString;
    $this->_CnxObject = null;
  }

  /**
   * Create a command for this database connection
   *
   * @return DbCommand Command for this database connection
   */
  public function createCommand() {
    return new DbCommand($this);
  }
}
