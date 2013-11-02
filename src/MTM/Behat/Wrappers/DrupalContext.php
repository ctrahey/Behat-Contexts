<?php

namespace MTM\Behat\Wrappers;

use Drupal\DrupalExtension\Event\EntityEvent;

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


  /**
   * @beforeUserCreate
   *
   * Note: This only works when using the @api driver and not the @drush driver.
   */
  public function setRoles(EntityEvent $event) {
    $user = $event->getEntity();
    if (!empty($user->roleIds)) {
      $user->roles = explode(',', $user->roleIds);
      unset($user->roleIds);
    }
  }

}
