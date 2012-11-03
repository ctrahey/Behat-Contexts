<?php
namespace MTM\Behat;
use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;

/**
 * Email context.
 */
class EmailContext extends BehatContext
{
  /**
   * @Given /^I have an empty inbox$/
   */
  public function iHaveAnEmptyInbox() {
    throw new PendingException();
  }
    
  
  
}