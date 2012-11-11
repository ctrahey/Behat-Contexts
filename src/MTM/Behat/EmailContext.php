<?php
namespace MTM\Behat;
use Behat\Behat\Exception\PendingException;

/**
 * Email context.
 */
class EmailContext extends SubContext
{
  /**
   * @Given /^I have an empty inbox$/
   */
  public function iHaveAnEmptyInbox() {
    throw new PendingException();
  }
    
  
  
}