<?php
/**
 * FastTrack engine
 *
 * @author Frederic BAYLE
 */

namespace FastTrack;

use FastTrack\Routing\Router;
use FastTrack\Controllers\ControllerBase;
use FastTrack\Security\XssInjection;

/**
 * FastTrack engine
 */
class Engine extends ObjectBase {
  /**
   * Display the command line help
   */
  public static function displayCliHelp() {
    echo "FastTrack application CLI Usage :\n";
    echo "php app_main_script.php command arg1 arg2 ...\n";
    echo "\n";
    echo "Commands :\n";
    echo " - action NameSpace\\ControllerClass.method   Execute a controller action\n";
    echo " - help                                       Display this message\n";
    echo "\n";
  }
  
  /**
   * Execute a controller action from the command line
   * 
   * @throws \Exception
   */
  public static function executeActionFromCli() {
    global  $argc;
    global  $argv;
    
    if ($argc < 3) {
      Engine::displayCliHelp();
      return;
    }
    
    // Compute class and method names
    $RxClassMethodResult = null;
    if(!preg_match('/(.+?)\.([^\.]+)$/', $argv[2], $RxClassMethodResult)) {
      // Bad class and method names format
      Engine::displayCliHelp();
      return;
    }
    
    $ScriptClassName = $RxClassMethodResult[1];
    $MethodName = $RxClassMethodResult[2];
    
    // Try to instanciate de controller class
    $Controller = new $ScriptClassName();
    if(!($Controller instanceof ControllerBase)) {
      throw new \Exception("The $ScriptClassName class must inherits from FastTrack\\Controllers\\ControllerBase.");
    }

    // Check if the action exists
    if(!method_exists($Controller,  $MethodName)) {
      throw new \Exception("The $ScriptClassName class doesn't contain the method $MethodName.");
    }
    
    // Execute the action
    echo $Controller->{$MethodName}($argv);
  }
  
  /**
   * Start the FastTrack engine
   *
   * @return  bool    This method returns false if CLI server and existing resource to serve, otherwise true
   */
  public static function start() {
    // Determine the engine mode
    if(php_sapi_name() == 'cli') {
      // CLI mode
      Engine::startCli();
    }
    else {
      // Web mode
      return Engine::startWeb();
    }
    
    return true;
  }
  
  /**
   * Start the CLI engine
   */
  public static function startCli() {
    global $argc;
    global $argv;
    
    // Checks for arguments
    if ($argc < 2) {
      Engine::displayCliHelp();
      return;
    }
    
    // Parsing arguments
    switch ($argv[1]) {
      case 'action':              // Execute a controller action
        Engine::executeActionFromCli();
        break;
      case 'help':
      default:                    // Unknown command, display help
        Engine::displayCliHelp();
        break;
    }
  }
  
  /**
   * Start the web engine
   *
   * @return  bool    This method returns false if CLI server and existing resource to serve, otherwise true
   */
  public static function startWeb() {
    // Look for a XSS injection attempt
    if(isset($_GET)) {
      XssInjection::checkRequestInjection($_GET, 'GET');
    }
    if(isset($_POST)) {
      XssInjection::checkRequestInjection($_POST, 'POST');
    }
    
    // Return the result and ends the response
    $OutputValue = Router::renderUrlToString($_SERVER['REQUEST_URI']);
    if($OutputValue === false) {
      return false;
    }
    
    echo $OutputValue;
    return true;
  }
}
