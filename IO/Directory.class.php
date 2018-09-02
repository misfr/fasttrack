<?php
/**
 * Directory manipulation class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\IO;

use FastTrack\ObjectBase;

/**
 * Directory manipulation class
 */
class Directory extends ObjectBase {
  /**
   * Create a directory with recursion
   *
   * @param string $pPath Path of the directory to create
   */
  public static function createDirectory($pPath) {
    mkdir($pPath, 0777, true);
  }

  /**
   * Enumerate directories in a given path
   *
   * @param   string      $pPath              Path to search in
   * @param   bool        $pRemoveParentDirs  Flag that indicates whether we must remove the . et .. directories from the results
   * @return  array                           Array containing the directories names
   * @throws  \Exception
   */
  public static function enumerateDirectories($pPath, $pRemoveParentDirs = false) {
    // Path doesn't exists -> error
    if(!Directory::exists($pPath)) {
      throw new \Exception("The path $pPath doesn't exist.");
    }

    // Read the path content
    $ReturnValue = [];
    if($DirHandle = opendir($pPath)) {
      while(false !== ($DirEntry = readdir($DirHandle))) {
        if(is_dir($pPath . '/' . $DirEntry)) {
          if($pRemoveParentDirs == false || ($DirEntry != '.' && $DirEntry != '..')) {
            $ReturnValue[] = $DirEntry;
          }
        }
      }
      closedir($DirHandle);
    }

    return $ReturnValue;
  }

  /**
   * Enumerate files in a given path
   *
   * @param   string      $pPath  Path to search in
   * @return  array               Array containing the files names
   * @throws  \Exception
   */
  public static function enumerateFiles($pPath) {
    // Path doesn't exists -> error
    if(!Directory::exists($pPath)) {
      throw new \Exception("The path $pPath doesn't exist.");
    }

    // Read the path content
    $ReturnValue = [];
    if($DirHandle = opendir($pPath)) {
      while(false !== ($DirEntry = readdir($DirHandle))) {
        if(!is_dir($pPath . '/' . $DirEntry)) {
          $ReturnValue[] = $DirEntry;
        }
      }
      closedir($DirHandle);
    }

    return $ReturnValue;
  }

  /**
   * Check if the given directory exists
   *
   * @param   string  $pPath  Path of the directory to check
   * @return  bool            True if the directory exists, otherwise false
   */
  public static function exists($pPath) {
    return file_exists($pPath) && is_dir($pPath);
  }

  /**
   * Delete a directory and its content
   *
   * @param string $pPath Path of the directory to delete
   */
  public static function delete($pPath) {
    // Deletes the directory content
    foreach(glob($pPath . '/*') as $FileItem) {
      is_dir($FileItem) ? Directory::delete($FileItem) : File::delete($FileItem);
    }

    // Then, deletes the directory
    rmdir($pPath);
  }

  /**
   * Rename or moves a directory
   *
   * @param string $pSourcePath   Path of the directory to delete or move
   * @param string $pDestPath     Destination path
   */
  public static function rename($pSourcePath, $pDestPath) {
    File::rename($pSourcePath, $pDestPath);
  }
}
