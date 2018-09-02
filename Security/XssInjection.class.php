<?php
/**
 * XSS Injection utility class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Security;

use FastTrack\ObjectBase;

/**
 * XSS Injection utility class
 */
class XssInjection extends ObjectBase {
  /**
   * Look for a XSS injection attempt in a request array such as GET, POST...
   *
   * @param   string      $pRequestArrayName  Name of the array to check
   * @param   array       $pRequestArray      Array to check
   * @throws  \Exception
   */
  public static function checkRequestInjection(&$pRequestArray, $pRequestArrayName) {
    // For each key of the request array
    foreach(array_keys($pRequestArray) as $ArrayKey) {
      if(is_array($pRequestArray[$ArrayKey])) {
        // The value is an array, do it recursive
        XssInjection::checkRequestInjection($pRequestArray[$ArrayKey], $pRequestArrayName);
      } else if(preg_match('/<\s*\/?\s*(?:script|link|form|i?frame|frameset|meta|input|select|button)/i', $pRequestArray[$ArrayKey])) {
        // A suspicious value was detected throw an exception
        throw new \Exception("XSS injection error : Suspicious value was detected ($pRequestArrayName).");
      }
    }
  }
}