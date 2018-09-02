<?php
/**
 * Controller base class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Controllers;

use FastTrack\ObjectBase;

/**
 * Controller base class
 */
class ControllerBase extends ObjectBase {
  /**
   * Get a flag that determines whether we could authorize a direct action call or not
   *
   * @param   string      $pMethodName Name of the method to check
   * @return  boolean                  True if allowed, otherwise false
   * @throws  \Exception
   */
  public function isActionUrlAllowed($pMethodName) {
    $ActionComment = (new \ReflectionClass(get_class($this)))->getMethod($pMethodName)->getDocComment();
    if(preg_match('/@allowActionUrl(\s|$)/i', $ActionComment)) {
      return true;
    }

    return false;
  }
}
