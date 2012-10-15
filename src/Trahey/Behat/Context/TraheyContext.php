<?php
namespace Trahey\Behat\Context;
use Drupal\DrupalExtension\Context\DrupalContext;
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';


class TraheyContext extends DrupalContext {
  /**
    * @Given /^I have browser which is supported$/
    */
   public function iHaveBrowserWhichIsSupported() {
     throw new PendingException();
   }

   /**
    * @When /^I visit the site$/
    */
   public function iVisitTheSite() {
     throw new PendingException();
   }

   /**
    * @Then /^I should get a valid page$/
    */
   public function iShouldGetAValidPage() {
     throw new PendingException();
   }  
}