<?php
namespace MTM\Behat\Service;

class Locations {
  
  /**
   * @property array the associative array of alias => path
   */
  protected $locations = array();
  
  public function __construct($locations = array()) {
    $keys = array_keys($locations);
    $keys = array_map('strtolower', $keys);
    $keys = array_map('trim', $keys);
    $this->locations = array_combine($keys, $locations);
  }

  public function resolve($alias) {
    $alias = trim(strtolower($alias));
    return $this->locations[$alias];
  }

}