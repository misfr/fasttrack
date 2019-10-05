<?php
/**
 * FastTrack configuration class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack;

/**
 * FastTrack configuration class
 */
class Config extends ObjectBase {
  /**
   * Custom configuration vars
   * 
   * ['CustomVarName' => 'CustomVarContent']
   *
   * @var array
   */
  public static $AppSettings = [];
  
  /**
   * Application sources absolute path
   * 
   * <p>This property must be set before loading the FastTrack engine</p>
   *
   * @var string
   */
  public static $AppSourcesPath;
  
  /**
   * Application web root absolute path
   *
   * <p>
   *   This property is the root directory that is served by your HTTP server for this application<br>
   *   It must be set before loading the FastTrack engine
   * </p>
   *
   * @var string
   */
  public static $AppWebRootPath;
  
  /**
   * Database connection strings
   * 
   * ['DataBaseName' => 'ConnectionStringContent']
   *
   * @var array
   */
  public static $ConnectionStrings = [];
  
  /**
   * Default crypt pass phrase
   * 
   * @var string
   */
  public static $CryptPassPhrase = 'FastTrack Rocks !!!';
  
  /**
   * Default crypt pass phrase
   *
   * @var string
   */
  public static $CryptSalt = 'Adding some salt...';
  
  /**
   * Default crypt initialization vector
   *
   * @var string
   */
  public static $CryptInitVector = '1n1tV3ct0r';
  
  /**
   * Flag that determines whether the debug mode is on or not
   * 
   * @var bool
   */
  public static $Debug = false;
  
  /**
   * Direct action call URL prefix
   *
   * @var string
   */
  public static $DirectActionUrlPrefix = '/_fasttrack/action/';

  /**
   * Route to render an error occurs
   *
   * @var string|null
   */
  public static $ErrorRouteName = null;
  
  /**
   * FastTrack root absolute path
   * 
   * @var string
   */
  public static $FastTrackPath = __DIR__;
  
  /**
   * Route to render if the given url was not found
   * 
   * @var string|null
   */
  public static $NotFoundRouteName = null;
  
  /**
   * Load configuration values from an array
   * 
   * @param   array   $pArray     Array of configuration values to load
   */
  public static function loadFromArray($pArray) {
    if(!is_array($pArray)) {
      // Given value is not an array, throw an exception
      throw new \Exception('The given value is not an array');
    }
    
    // Copy each array key to the corresponding static property
    foreach ($pArray as $ConfigKey => $ConfigValue) {
      Config::${$ConfigKey} = $ConfigValue;
    }
  }
  
  /**
   * Compute a path according to the application configuration
   * 
   * <p>Available commands :</p>
   * <ul>
   *   <li>~/ : Application sources path</li>
   *   <li>@/ : Application web root path</li>
   * </ul>
   * 
   * @param   string  $pInputPath     Path to compute
   * @return  string
   */
  public static function mapPath($pInputPath) {
    $ReturnValue = $pInputPath;
    if(preg_match('/^~\//', $ReturnValue)) {
      // Application root path
      $ReturnValue = preg_replace('/^~\//', str_replace('\\', '\\\\', Config::$AppSourcesPath) . '/', $ReturnValue);
    }
    else if(preg_match('/^@\//', $ReturnValue)) {
      // Application root path
      $ReturnValue = preg_replace('/^@\//', str_replace('\\', '\\\\', Config::$AppWebRootPath) . '/', $ReturnValue);
    }
    
    return $ReturnValue;
  }
}
    