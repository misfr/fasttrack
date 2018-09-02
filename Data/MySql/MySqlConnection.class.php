<?php
/**
 * MySQL database connection
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data\MySql;

use FastTrack\Data\DbConnection;
use FastTrack\Data\DbConnectionParameters;

/**
 * MySQL database connection
 */
class MySqlConnection extends DbConnection {
  /**
   * class constructor
   *
   * @param string $pConnectionString MySQL database connexion string
   */
  public function __construct($pConnectionString) {
    parent::__construct($pConnectionString);
    
    // Extracts connection parameters
    $CnxParams = new DbConnectionParameters($pConnectionString);
    $PDOCnxStr = 'mysql:host=' . $CnxParams->ConnectionParameters['host'] . ';' . 'port=' . $CnxParams->ConnectionParameters['port'] . ';' . 'dbname=' . $CnxParams->ConnectionParameters['dbname'] . ';' . 'charset=' . $CnxParams->ConnectionParameters['charset'] . ';';
    
    // If connection was never used, opening and caching it
    if(!array_key_exists($pConnectionString, DbConnection::$ConnectionPool)) {
      DbConnection::$ConnectionPool[$pConnectionString] = new \PDO($PDOCnxStr, $CnxParams->ConnectionParameters['user'], $CnxParams->ConnectionParameters['password']);
    }
    
    $this->_CnxObject = DbConnection::$ConnectionPool[$pConnectionString];
  }
  
  /**
   * Create a command for this database connection
   *
   * @return MySqlCommand Command for this database connection
   */
  public function createCommand() {
    return new MySqlCommand($this);
  }
}
