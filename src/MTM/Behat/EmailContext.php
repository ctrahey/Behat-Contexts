<?php
namespace MTM\Behat;
use Behat\Behat\Exception\PendingException;

/**
 * Email context.
 * @todo ... this is just an idea... 
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