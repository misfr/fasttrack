<?php
/**
 * File handling utility class
 *
 * @author  Frederic BAYLE
 */

namespace FastTrack\IO;

use FastTrack\ObjectBase;

/**
 * File handling utility class
 * 
 * @property-read bool $EOF Get a flag that determines whether the end of file has been reached
 */
class File extends ObjectBase {
  /**
   * File opening mode: Read only
   * 
   * @var int
   */
  const MODE_READONLY = 0;
  
  /**
   * File opening mode: Write only
   *
   * @var int
   */
  const MODE_WRITEONLY = 1;
  
  /**
   * File opening mode: Write only at the end of a file
   *
   * @var int
   */
  const MODE_WRITEONLYAPPEND = 2;
  
  /**
   * File resource handler (used by close, readBytes and writeBytes functions)
   * 
   * @var resource
   */
  protected $_FileHandler = NULL;

  /**
   * Get a flag that determines whether the end of file has been reached
   * 
   * @return bool
   * @throws \Exception
   */
  protected function &getEOF() {
    if($this->_FileHandler !== NULL) {
      throw new \Exception('This file is closed.');
    }
    $ReturnValue = feof($this->_FileHandler);
    return $ReturnValue;
  }

  /**
   * Create an instance of this class, opens a file
   *
   * @param string    $pFilePath Path of the file to open
   * @param int       $pFileMode Opening Mode (consts in  the FileMode class)
   */
  function __construct($pFilePath, $pFileMode) {
    $FileMode = '';
    switch(pFileMode) {
      case File::MODE_WRITEONLY:
        $FileMode = 'wb';
        break;
      case File::MODE_WRITEONLYAPPEND:
        $FileMode = 'ab';
        break;
      case File::MODE_READONLY:
      default:
        $FileMode = 'rb';
        break;
    }
    $this->_FileHandler = fopen($pFilePath, $FileMode);
  }

  /**
   * Class destructor
   */
  public function __destruct() {
    if($this->_FileHandler !== NULL) {
      $this->close();
    }
  }

  /**
   * Close the current opened file
   * 
   * @throws \Exception
   */
  public function close() {
    if($this->_FileHandler === NULL) {
      throw new \Exception('This file is already closed.');
    }
    fclose($this->_FileHandler);
    $this->_FileHandler = NULL;
  }

  /**
   * Delete a file or a symbolic link from the file system
   *
   * @param   string  $pFilePath  Path od the file to delete
   * @return  bool                True on success, otherwise false
   */
  public static function delete($pFilePath) {
    return unlink($pFilePath);
  }

  /**
   * Check if a file exists
   *
   * @param   string  $pFilePath  Path of the file that we need to check the existence
   * @return  bool                True if the given file exists, otherwise false
   */
  public static function exists($pFilePath) {
    return file_exists($pFilePath) && is_file($pFilePath);
  }

  /**
   * Read and returns all file content as a string
   *
   * @param   string  $pFilePath  Path of the file to read
   * @return  string              File content
   */
  public static function readAllText($pFilePath) {
    return file_get_contents($pFilePath);
  }

  /**
   * Read bytes from a file
   *
   * @param   int         $pCount     Number of bytes to read
   * @return  array                   Array of bytes. Its size can be lesser than $pCount if the end of file is reached
   * @throws  \Exception
   */
  public function readBytes($pCount) {
    if($this->_FileHandler === null) {
      // No file opened
      throw new \Exception('You must open a file before reading bytes.');
    }

    // Reads bytes from file
    $Result = fread($this->_FileHandler, $pCount);

    // Converts the result to a byte array
    $ReturnValue = array_values(unpack('C*', $Result));

    return $ReturnValue;
  }

  /**
   * Rename or moves a file
   *
   * @param string $pSourcePath   Path of the file to rename or move
   * @param string $pDestPath     Destination path
   */
  public static function rename($pSourcePath, $pDestPath) {
    rename($pSourcePath, $pDestPath);
  }

  /**
   * Write a text content into a file
   *
   * @param string $pFilePath     Path of the file to write into
   * @param string $pTextContent  Text content to write
   */
  public static function writeAllText($pFilePath, $pTextContent) {
    file_put_contents($pFilePath, $pTextContent);
  }

  /**
   * Write bytes to an opened file
   *
   * @param   array       $pData  Array of bytes to write into the file
   * @return  int                 Number of bytes that have been written
   * @throws  \Exception
   */
  public function writeBytes($pData) {
    if($this->_FileHandler === null) {
      // No file opened
      throw new \Exception('You must open a file before writing bytes.');
    }

    // Writes bytes into the file
    $Data = '';
    foreach($pData as $currentByte) {
      $Data .= pack('C*', $currentByte);
    }
    $ReturnValue = fwrite($this->_FileHandler, $Data);

    if($ReturnValue === false) {
      // Unable to write
      throw new \Exception('Unable to write bytes into this file.');
    }

    return $ReturnValue;
  }
}
