<?php
namespace MTM\Behat;
use Behat\Behat\Exception\PendingException;
use MTM\Behat\Service\Email;

/**
 * Email context.
 */
class EmailContext extends SubContext
{

  protected $mailService = NULL;

  public function __construct() {
    $this->mailService = new Email("/var/mail/jonathan");
  }

  /**
   * @Given /^I have an empty inbox$/
   */
  public function iHaveAnEmptyInbox() {
    $this->mailService->_empty();
  }

  /**
   * @Then /^I should receive an email "(?P<subject>(?:[^"]|\\")*)"(?: to "(?P<to>[^"]*)")?(?: from "(?P<from>[^"]*)")?$/
   */
  public function iShouldReceiveAnEmail($subject, $to = FALSE, $from = FALSE) {
    $this->mailService->reset();
    while ($msg = $this->mailService->read()) {
      // If we provided a to address and that doesn't match, skip this message
      if ($to && $to != $msg['to']) {
        continue;
      }
      // If we provided a from address adn that doesn't match, skip this message
      if ($from && $from != $msg['from']) {
        continue;
      }
      // If we got here and the subject matches, return successfully.
      if ($msg['subject'] == $subject) {
        return;
      }
    }
    // If we got to this point, we didn't find the expected email.
    $desc = 'Subject: ' . $subject;
    if ($to) {
      $desc .= ' To: ' . $to;
    }
    if ($from) {
      $desc .= ' From: ' . $from;
    }
    throw new \Exception("Email message $desc not found.");
  }


}
