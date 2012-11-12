<?php
namespace MTM\Behat;

/**
 * Metal Toad's base subcontext
 * All tag-loadable subcontexts should extend this.
 */
class SubContext extends \Behat\Behat\Context\BehatContext {
  public function __call($method, $args) {
    return call_user_func_array(array($this->getMainContext(), $method), $args);
  } 
}