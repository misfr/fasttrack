<?php
/**
 * FastTrack class autoloader
 *
 * @author Frederic BAYLE
 */

namespace FastTrack;

require(__DIR__ . '/ObjectBase.class.php');
require(__DIR__ . '/Str.class.php');

/**
 * FastTrack class autoloader
 *
 * <p>This class tries to autoload unknown classes using differents naming conventions</p>
 * <ul>
 *   <li>First, it looks for a FastTrack internal class</li>
 *   <li>Then, it looks for a class that is in the application sources path</li>
 * </ul>
 * <p>
 *   <em>Ex: Models\TestClass will be searched in the /ApplicationSources/Models/TestClass.php and  /ApplicationSources/Models/TestClass.class.php files.</em><br>
 *   Note that filenames are case sensitive on unix based systems.
 * </p>
 */
class Autoloader extends ObjectBase {
  /**
   * Register the class autoloading function
   */
  public static function register() {
      spl_autoload_register([Autoloader::class, 'tryLoadClass']);
  }
  
  /**
   * Try to load automatically a PHP script file from a given classname
   *
   * @param   string  $pName  Classname
   */
  public static function tryLoadClass($pName) {
    if(Str::startsWith($pName, __NAMESPACE__ . '\\')) {
      // This is a FastTrack internal class, check if file exists
      $ClassFileName = str_replace(__NAMESPACE__ . '\\', __DIR__ . '/', $pName); 
      $ClassFileName = str_replace('\\', '/', $ClassFileName);
      $ClassFileName .= '.class.php';
      if(file_exists($ClassFileName)) {
        // The file exists, try to load it
        require($ClassFileName);
      }
    }
    else {
      // This is not a FastTrack internal class, determine all the file name possibilities
      $TmpName = str_replace('\\', '/', $pName);
      $ClassFileNames = [
        Config::$AppSourcesPath . '/' . $TmpName . '.class.php',
        Config::$AppSourcesPath . '/' . $TmpName . '.php'
      ];
      foreach ($ClassFileNames as $ClassFileName)	{
        // For each possibility
        if(file_exists($ClassFileName)) {
          // The file exists, try to load it
          require($ClassFileName);

          // Exit loop
          break;
        }
      }
    }
  }
}
