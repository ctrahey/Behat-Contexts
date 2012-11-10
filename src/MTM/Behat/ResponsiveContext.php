<?php
namespace MTM\Behat;

/**
 * Misc context.
 */
class ResponsiveContext extends \Behat\Behat\Context\BehatContext
{
  /**
   * @Given /^(?:|I )am on a smartphone$/
   */
  public function amOnASmartphone()
  {
      $this->getSession()->resizeWindow(520, 700);
  }
  
  /**
   * @Given /^(?:|I )am on a desktop$/
   */
  public function amOnADesktop()
  {
      $this->getSession()->getDriver()->resizeWindow(1024, 768);
  }
  
}
