<?php
/**
 * Database connection parameters
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack\Data;

use FastTrack\ObjectBase;

/**
 * Database connection parameters
 *
 * Theses parameters are extracted from a database connection string as an associative array.
 * Required keys ares Host, Port, DbName, User and Password (Charset is optionnal, utf8 is set by default)
 */
class DbConnectionParameters extends ObjectBase {
  /**
   * Database connection parameters
   *
   * Default values :<br>
   * <pre>
   * 01 [
   * 02 'host' => 'localhost',
   * 03 'port' => '3306',
   * 04 'dbname' => 'db',
   * 05 'user' => 'root',
   * 06 'password' => 'pwd',
   * 07 'charset' => 'utf8',
   * 11 ]
   * </pre>
   *
   * @var array
   */
  public $ConnectionParameters;

  /**
   * Class constructor
   *
   * @param   string      $pConnectionString  Database connection string (format : host=localhost;port=3306;dbname=db;user=root;password=pwd)
   * @throws  \Exception
   */
  public function __construct($pConnectionString) {
    $RxCnxStrResult = null;
    if(!preg_match_all('/\s*([^;]+)\s*=\s*([^;]+)\s*/i', $pConnectionString, $RxCnxStrResult)) {
      // Bad format
      throw new \Exception("Bad connection string format : $pConnectionString.");
    }
    
    // Initializations
    $this->ConnectionParameters = [
      'host' => 'localhost',
      'port' => '3306',
      'dbname' => 'db',
      'user' => 'root',
      'password' => 'pwd',
      'charset' => 'utf8',
    ];

    // Trying to extract parameters from the connection string
    if(count($RxCnxStrResult) > 0) {
      // Gets each parameter with its value
      for($i = 0; $i < count($RxCnxStrResult[1]); $i++) {
        $this->ConnectionParameters[$RxCnxStrResult[1][$i]] = $RxCnxStrResult[2][$i];
      }
    }
  }
}
