<?php

namespace MTM\Behat\Wrappers;

/**
 * This class is a hack.
 * It is designed solely to allow calling protected methods from elsewhere.
 * Use it in place of the extended class when you need to use an instance
 * in a composition pattern the same way you would with extension.
 */
class DrupalContext extends \Drupal\DrupalExtension\Context\DrupalContext {
  
  public function __call($method, $args) {
    if(method_exists($this, $method)) {
      return call_user_func_array(array($this, $method), $args);
    }
  }
  
}