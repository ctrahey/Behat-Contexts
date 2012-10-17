<?php
namespace MTM\Behat\Context;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

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