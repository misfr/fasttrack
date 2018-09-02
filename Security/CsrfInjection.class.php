<?php
/**
 * CSRF Injection utility class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Security;

use FastTrack\ObjectBase;

/**
 * CSRF Injection utility class
 */
class CsrfInjection extends ObjectBase {
  /**
   * Generate a CSRF token
   * 
   * @return string CRSF token  
   */
  public static function generateCsrfToken() {
    $Now = new \DateTime();

    if(isset($_SESSION['__csrfToken']) && isset($_SESSION['__csrfTokenValidity'])) {
      // if a token exists in the current session, check its validity
      $TokenValidityDate = \DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['__csrfTokenValidity']);

      if($TokenValidityDate >= $Now) {
        // Current token is valid, add 30min of validity and return it
        $_SESSION['__csrfTokenValidity'] = $Now->add(new \DateInterval('PT30M'))->format('Y-m-d H:i:s'); // h + 30min
        return $_SESSION['__csrfToken'];
      }
    }

    // We must generate a new token
    $Token = uniqid(rand(), true);

    // Enregistre le token dans la session
    $_SESSION['__csrfToken'] = $Token;
    $_SESSION['__csrfTokenValidity'] = $Now->add(new \DateInterval('PT30M'))->format('Y-m-d H:i:s'); // h + 30min

    return $Token;
  }

  /**
   * check a CSRF token validity
   *
   * @return bool True on success, otherwise false
   */
  public static function checkCsrfToken() {
    $Now = new \DateTime();
    $ReturnValue = false;
    $TokenToCheck = '';
    if(isset($_SERVER['HTTP_CSRFTOKEN'])) {
      // Search in the request headers
      $TokenToCheck = $_SERVER['HTTP_CSRFTOKEN'];
    } else if(isset($_POST)) {
      // We must search token in the POST
      if(isset($_POST['__csrfToken'])) {
        $TokenToCheck = $_POST['__csrfToken'];
      }
    }

    // Compare tokens and check validity
    if($TokenToCheck != '' && isset($_SESSION['__csrfToken']) && isset($_SESSION['__csrfTokenValidity'])) {
      $TokenValidityDate = \DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['__csrfTokenValidity']);
      if($TokenToCheck == $_SESSION['__csrfToken'] && $TokenValidityDate >= $Now) {
        $ReturnValue = true;
      }
    }

    return $ReturnValue;
  }
}
