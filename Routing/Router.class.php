<?php
/**
 * FastTrack router
 *
 * @author  Frederic BAYLE
 */

namespace FastTrack\Routing;

use FastTrack\Config;
use FastTrack\ObjectBase;
use FastTrack\Controllers\ControllerBase;
use FastTrack\Security\XssInjection;
use FastTrack\Str;

/**
 * FastTrack router
 */
class Router extends ObjectBase {
  /**
   * Routes table collection
   *
   * @var RouteItem[]
   */
  public static $RoutesTable = [];
  
  /**
   * Add a route to the routes table collection
   *
   * @param   string      $pRouteName     Name of the route to add
   * @param   RouteItem   $pNewRouteItem  Route to add
   */
  public static function add($pRouteName, $pNewRouteItem) {
    Router::$RoutesTable[$pRouteName] = $pNewRouteItem;
  }
  
  /**
   * Render a route to a string
   * 
   * @param   string      $pRouteName     Name of the route to render
   * @param   array       $pParameters    Parameters to pass to the action
   * @throws  \Exception
   */
  public static function renderRouteToString($pRouteName, $pParameters) {
    // Try to get the given route
    if(!array_key_exists($pRouteName, Router::$RoutesTable)) {
      throw new \Exception("Unable to find the route named $pRouteName in the routes table.");
    }
    $Route = Router::$RoutesTable[$pRouteName];
    
    // Add the defaults parameters
    foreach ($Route->DefaultsParameters as $ParamKey => $ParamValue) {
      if(!array_key_exists($ParamKey, $pParameters)) {
        $pParameters[$ParamKey] = $ParamValue;
      }
    }
    // Try to instanciate the controller
    $Controller = new $Route->ControllerClassName();
    if(!($Controller instanceof ControllerBase)) {
      throw new \Exception("The {$Route->ControllerClassName} class must inherit from FastTrack\\Controllers\\ControllerBase.");
    }
    
    // Check if the given action exists
    if(!method_exists($Controller, $Route->Action)) {
      throw new \Exception("The {$Route->ControllerClassName} class doesn't contain the method {$Route->Action}.");
    }
    
    // Return the action result
    return $Controller->{$Route->Action}($pParameters);
  }
  
  /**
   * Render an URL to a string
   *
   * @param   string          $pUrl   URL to render
   * @return  string|false            Rendered route as string. This method returns false if in CLI server mode and existing resource to serve
   */
  public static function renderUrlToString($pUrl) {
    // Get url components
    $UrlComponents = explode('?', $pUrl);
    $Url = $UrlComponents[0];
    
    // If in CLI server mode, try to serve the requested file if it exists
    if(php_sapi_name() == 'cli-server') {
      $RealRequestedFilePath = Config::$AppWebRootPath . $Url;
      
      // Checks if the file exists
      if(file_exists($RealRequestedFilePath)) {
        // The requested file exists, return false to tell to the PHP engine to serve the file
        return false;
      }
    }

    // Check if the given URL is a FastTrack resource
    if(Str::startsWith($Url, '/_fasttrack/action/')) {
      // Direct action call, extract ClassName and method
      $RxActionCallResult = null;
      if(!preg_match('/^\/_fasttrack\/action\/(?<controller>.+?)\.(?<method>.+?)(?:\?.+)?$/', $Url, $RxActionCallResult)) {
        throw new \Exception("The given url doesn't match the direct action call protocol (/_fasttrack/action/Namespace/Controller.method?param1=xxx).");
      }
      
      // Try to instanciate the controller
      $ControllerClassName = str_replace('/', '\\', $RxActionCallResult['controller']);
      $Controller = new $ControllerClassName();
      if(!($Controller instanceof ControllerBase)) {
        throw new \Exception("The $ControllerClassName class must inherit from FastTrack\\Controllers\\ControllerBase.");
      }
      
      // Check if the given action exists
      if(!method_exists($Controller, $RxActionCallResult['method'])) {
        throw new \Exception("The $ControllerClassName class doesn't contain the method {$RxActionCallResult['method']}.");
      }
      
      // Check if this method accepts a direct action call
      if(!$Controller->isActionUrlAllowed($RxActionCallResult['method'])) {
        throw new \Exception("The method $ControllerClassName::{$RxActionCallResult['method']} doesn't accept direct action call (you must add the @allowActionUrl PHPDoc annotation).");
      }
      
      // Return the action result
      return $Controller->{$RxActionCallResult['method']}(isset($_GET) ? $_GET : []);
    }
    
    // Check each key of the routes table to find the one corresponding to the given URL
    foreach (Router::$RoutesTable as $RouteName => $Route) {
      $ActionParameters = null;
      if (preg_match('/' . str_replace('/', '\/', $Route->Pattern) . '/', $Url, $ActionParameters) &&
        ($Route->Methods == '*' || Str::contains(',' . $Route->Methods . ',', ',' . $_SERVER['REQUEST_METHOD'] . ','))) {
        // Look for a XSS injection attempt
        XssInjection::checkRequestInjection($ActionParameters, 'Route pattern captures');
        
        // The route pattern and the method matche with the given URL, 
        return Router::renderRouteToString($RouteName, $ActionParameters);
      }
    }
    
    // No route found, display a not found message/route
    if(Config::$NotFoundRouteName !== null) {
      // Not found route defined, render it
      return Router::renderRouteToString(Config::$NotFoundRouteName, ['Url' => $Url]);
    }
    else {
      // No route to render, display a simple not found message
      header('HTTP/1.0 404 Not Found');
      header('content-type: text/plain');
      return 'Not found';
    }
  }
}
