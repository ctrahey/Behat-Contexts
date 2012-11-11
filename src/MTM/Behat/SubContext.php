<?php
namespace MTM\Behat;

class SubContext extends \Behat\Behat\Context\BehatContext {
  public function __call($method, $args) {
    if(method_exists($this->getMainContext(), $method)) {
      return call_user_func_array(array($this->getMainContext(), $method), $args);
    }
    return parent::__call($method, $args);
  } 
}