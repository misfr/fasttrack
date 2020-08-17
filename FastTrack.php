<?php
/**
 * Main script
 *
 * @author Frederic BAYLE
 */

namespace FastTrack;

use FastTrack\Routing\Router;

require(__DIR__ . '/Autoloader.class.php');

// Register autoloading function
Autoloader::register();

/**
 * PHP Error handling
 *
 * This function handles classical PHP errors
 *
 * @param   int     $severity   Error severity
 * @param   string  $message    Error message
 * @param   string  $file       File where error occured
 * @param   int     $line       Line where error occured
 * @ignore
 */
function fasttrackErrorHandler($severity, $message, $file, $line) {
  $Err = new \ErrorException($message, 0, $severity, $file, $line, null);
  if(Config::$ErrorRouteName !== null) {
    // Error route defined, render it
    while(ob_get_level() > 0) {
      ob_end_clean();
    }
    echo Router::renderRouteToString(Config::$ErrorRouteName, ['Exception' => $Err]);
  }
  else {
    // No route to render, display a simple error message
    header("HTTP/1.0 500 Internal Server Error");
    header('content-type: text/plain');
    echo $Err->__toString();
  }
  die();
}

/**
 * PHP Exception handling
 *
 * @param   \Exception  $exception  Unhandled exception
 * @ignore
 */
function fasttrackExceptionHandler($exception) {
  if(Config::$ErrorRouteName !== null) {
    // Error route defined, render it
    while(ob_get_level() > 0) {
      ob_end_clean();
    }
    echo Router::renderRouteToString(Config::$ErrorRouteName, ['Exception' => $exception]);
  }
  else {
    // No route to render, display a simple error message
    header("HTTP/1.0 500 Internal Server Error");
    header('content-type: text/plain');
    echo $exception->__toString();
  }
  die();
}

/**
 * PHP shutdown handling
 *
 * This function tries to handle unhandled or fatal errors
 *
 * @ignore
 */
function fasttrackShutDownHandler() {
  // Fatal error handling
  $last_error = error_get_last();

  // If fatal error occured, convert it to an exception
  if ($last_error !== NULL ? $last_error['type'] == E_ERROR : false) {
    fasttrackErrorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
  }
}

if(php_sapi_name() != 'cli') {
  // Set custom PHP error handlers if not in cli mode
  set_exception_handler('FastTrack\fasttrackExceptionHandler');
  set_error_handler('FastTrack\fasttrackErrorHandler');
  register_shutdown_function('FastTrack\fasttrackShutDownHandler');
}
