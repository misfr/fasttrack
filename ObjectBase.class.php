<?php
/**
 * Object base class
 * 
 * @author Frederic BAYLE
 */

namespace FastTrack;

/**
 * Object base class
 */
class ObjectBase {
  /**
   * Magic getter method
   * 
   * <p>
   *   This method get the value of a virtual property of this class.<br>
   *   It looks for a method starting with get.<br>
   *   <em>Ex : <code>return $MyObject-&gt;MyProperty;</code> will call <code>return $MyObject-&gt;getMyProperty();</code></em>
   * </p>
   * 
   * @param   string      $pName  Name of the virtual property
   * @return  mixed
   * @throws  \Exception
   */
  function &__get($pName) {
    if(method_exists($this, 'get' . $pName)) {
      // A method starting with get exists, run it
      return $this->{'get' . $pName}();
    }
    
    // No getter method, throw an exception
    throw new \Exception("Unable to find a virtual property named $pName (get$pName method).");
  }
  
  /**
   * Magic setter method
   * 
   * <p>
   *   This method set the value of a virtual property of this class.<br>
   *   It looks for a method starting with set.<br>
   *   <em>Ex : <code>$MyObject-&gt;MyProperty = 'value';</code> will call <code>$MyObject-&gt;setMyProperty('value');</code></em>
   * </p>
   * 
   * @param   string      $pName      Name of the virtual property
   * @param   mixed       $pValue     New value of the virtual property
   */
  function __set($pName, $pValue) {
    if(method_exists($this, 'set' . $pName)) {
      // A method starting with set exists, run it
      return $this->{'set' . $pName}($pValue);
    }
    
    // No setter method, simply assign property
    $this->{$pName} = $pValue;
  }
  
  /**
   * Return a string representation of this class
   * 
   * @return string
   */
  function __toString() {
    return get_class($this);
  }
  
  /**
   * Return a string representation of this class
   * 
   * @see     ObjectBase::__toString()
   * @return  string
   */
  public function toString() {
    return $this->__toString();
  }
}