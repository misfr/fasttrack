<?php
/**
 * File statistics utility class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\IO;

use FastTrack\ObjectBase;

/**
 * File statistics utility class
 * 
 * @property-read \DateTime $CreationTime   Get the creation time of the item
 * @property-read string    $DirectoryName  Get an instance of the parent directory
 * @property-read bool      $Exists         Get a flag the determines whether the item exists
 * @property-read string    $Extension      Get the extension of the item
 * @property-read string    $FullName       Get the full name of the item
 * @property-read bool      $IsDirectory    Get a flag that determines whether this item is a directory
 * @property-read bool      $IsFile         Get a flag that determines whether this item is a file
 * @property-read \DateTime $LastAccessTime Get the last access time of the item
 * @property-read \DateTime $LastWriteTime  Get the last write time of the item
 * @property-read int       $Length         Get length of the item in bytes
 * @property-read string    $Name           Get the name of the item
 */
class FileInfo extends ObjectBase {
  /**
   * Get the creation time of the item
   *
   * @return \DateTime
   * @throws \Exception
   */
  protected function &getCreationTime() {
    if(!$this->Exists) {
      throw new \Exception("The item {$this->_FullName} doesn't exist.");
    }
    $ReturnValue = new \DateTime('@' . filectime($this->_FullName));
    return $ReturnValue;
  }

  /**
   * Get the name of the parent directory
   *
   * @return string
   */
  protected function &getDirectoryName() {
    $ReturnValue = dirname($this->_FullName);
    return $ReturnValue;
  }

  /**
   * Get a flag that determines whether the item exists
   * 
   * @return bool
   */
  protected function &getExists() {
    $ReturnValue = file_exists($this->_FullName);
    return $ReturnValue;
  }

  /**
   * Get the extension of the item
   *
   * @return string
   */
  protected function &getExtension() {
    $ReturnValue = pathinfo($this->_FullName);
    return $ReturnValue['extension'];
  }

  /**
   * Get the full name of the item
   *
   * @return string
   */
  protected function &getFullName() {
    return $this->_FullName;
  }
  /**
   * Full name of the item
   * 
   * @var string
   */
  protected $_FullName;

  /**
   * Get a flag that determines whether this item is a directory
   *
   * @return bool
   */
  protected function &getIsDirectory() {
    $ReturnValue = false;
    if($this->Exists) {
      $ReturnValue = is_dir($this->_FullName);
    }
    return $ReturnValue;
  }

  /**
   * Get a flag that determines whether this item is a file
   *
   * @return bool
   */
  protected function &getIsFile() {
    $ReturnValue = false;
    if($this->Exists) {
      $ReturnValue = is_file($this->_FullName);
    }
    return $ReturnValue;
  }

  /**
   * Get the last access time of the item
   *
   * @return \DateTime
   * @throws \Exception
   */
  protected function &getLastAccessTime() {
    if(!$this->Exists) {
      throw new \Exception("The item {$this->_FullName} doesn't exist.");
    }
    $ReturnValue = new \DateTime('@' . fileatime($this->_FullName));
    return $ReturnValue;
  }

  /**
   * Get the last write time of the item
   *
   * @return \DateTime
   * @throws \Exception
   */
  protected function &getLastWriteTime() {
    if(!$this->Exists) {
      throw new \Exception("The item {$this->_FullName} doesn't exist.");
    }
    $ReturnValue = new \DateTime('@' . filemtime($this->_FullName));
    return $ReturnValue;
  }

  /**
   * Get length of the item in bytes
   *
   * @return int
   * @throws \Exception
   */
  protected function &getLength() {
    if(!$this->Exists) {
      throw new \Exception("The item {$this->_FullName} doesn't exist.");
    }
    $ReturnValue = filesize($this->_FullName);
    return $ReturnValue;
  }

  /**
   * Get the name of the item
   *
   * @return string
   */
  protected function &getName() {
    $ReturnValue = pathinfo($this->_FullName);
    return $ReturnValue['basename'];
  }

  /**
   * Class constructor
   *
   * @param string $pFullName Full name of the item to get statistics from
   */
  function __construct($pFullName) {
    // Initializations
    $this->_FullName = $pFullName;
  }
}
	