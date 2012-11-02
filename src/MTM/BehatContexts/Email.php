<?php
namespace MTM\BehatContexts;
use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;

/**
 * Email context.
 */
class Email extends BehatContext
{
  /**
     * @Given /^I have an empty inbox$/
     */
    public function iHaveAnEmptyInbox() {
      throw new PendingException();
    }
  
  
}