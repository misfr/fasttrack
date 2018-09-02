<?php
/**
 * Route item class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Routing;

use FastTrack\ObjectBase;

/**
 * Route item class
 */
class RouteItem extends ObjectBase {
  /**
   * Action to execute in the given controller
   *
   * @var string
   */
  public $Action;
  
  /**
   * Name of the controller class to instanciate
   *
   * @var string
   */
  public $ControllerClassName;
  
  /**
   * Defaults parameters to pass to the action
   *
   * @var array
   */
  public $DefaultsParameters;
  
  /**
   * Coma separated methods that are allowed for this route item, * = all methods are allowed
   *
   * ex : GET,POST,PUT,DELETE,PATCH
   *
   * @var string
   */
  public $Methods;
  
  /**
   * Regular expression pattern used to test if this route item matches with the given URL
   *
   * @var string
   */
  public $Pattern;
  
  /**
   * Class contructor
   *
   * @param   string  $pPattern               Pattern used to test if this route item matches with the given URL
   * @param   string  $pControllerClassName   Name of the controller class to instanciate
   * @param   array   $pAction                Action to execute
   * @param   array   $pMethods               Methods that will trig this route
   * @param   array   $pDefaultParameters     Associative array containing the default parameters to pass to the action
   */
  public function __construct($pPattern, $pControllerClassName, $pAction, $pMethods = '*', $pDefaultParameters = []) {
    // Initializations
    $this->Pattern = $pPattern;
    $this->ControllerClassName = $pControllerClassName;
    $this->Action = $pAction;
    $this->Methods = $pMethods;
    $this->DefaultsParameters = $pDefaultParameters;
  }
}
