<?php
namespace MTM\Behat;
use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

/**
 * Misc context.
 * A library of generic or overtly useful step definitions.
 * As these grow, consider creating dedicated subcontexts
 * for meaningful groups.
 *
 * This subcontext is always loaded.
 */
class MiscContext extends SubContext
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


  /**
   * @Given /^(?:|I )am creating content of type "(?P<type>(?:[^"]|\\")*)"$/
   */
  public function createContent($type)
  {
      return new Given(sprintf('I am on "/node/add/%s"', $type));
  }

  /**
   * @When /^(?:|I )wait for "(?P<element>[^"]*)" to be visible$/
   */
  public function waitForVisible($element)
  {
      $this->getSession()->wait(5000, "jQuery('" . $element . "').is(':visible')");
  }

  /**
   * @Then /^the page title should be "(?P<text>(?:[^"]|\\")*)"$/
   */
  public function valuePageTitle($text)
  {
    return new Then(sprintf('I should see "%s" in the "%s" element', $text, 'h1'));
  }

  /**
  * @Then /^I should see an alert$/
  */
  public function iShouldSeeAnAlert() {
    // https://github.com/Behat/Mink/issues/158
    $alert = $this->getSession()->getDriver()->wdSession->getAlert_text();
    if ($alert) {
      $this->confirmPopup();
      return $this->confirmPopup();
    }
    else  {
      throw new Exception("No alert found!" . $this->output);
    }
  }

  /**
  * @when /^(?:|I )confirm the popup$/
  */
  public function confirmPopup()
  {
      $this->getSession()->getDriver()->wdSession->accept_alert();
  }

  /**
   * Select an option based on CSS seelctor
   *
   * @When /^(?:|I )pick "(?P<option>(?:[^"]|\\")*)" from "(?P<selector>[^"]*)"$/
   */
  public function selectFieldBySelector($option, $selector)
  {
    $field = $this->getSession()->getPage()->find('css', $selector);
    $field->selectOption($option);
  }

  /**
   * @Given /^I wait for "([^"]*)" seconds?$/
   * @Given /^I wait for ([^"]*) seconds?$/
   */
  public function iWaitForSeconds($sec) {
    $this->getSession()->wait((int)$sec * 1000);
  }

  /**
   * @When /^I enter "(?P<offset>[^"]*)" from "(?P<basetime>[^"]*)" for "(?P<field>(?:[^"]|\\")*)"$/
   */
  public function iEnterTimeForField($offset, $basetime, $field) {
    $base = strtotime($basetime); // This could be now, yesterday
    $time = strtotime($offset, $base);
    $output = date('Y-m-d H:i:s', $time);
    return new When("I enter \"$output\" for \"$field\"");
  }


  /**
   * @Given /^I masquerade as "([^"]*)"$/
   */
  public function iMasqueradeAs($user) {
    $user_url = str_replace(" ","-", strtolower($user));
    $this->iAmLoggedInWithRole('programmer');
    $this->getSession()->visit($this->locatePath('/users/' . $user_url));
    $this->getSession()->getPage()->clickLink('Masquerade as ' . $user);
  }

  public function assertTrue($subject, $message = 'The tested subject was not truthy.') {
    if(!(bool)$subject) {
      throw new \Exception($message);
    }
  }

  public function assertFalse($subject, $message = 'The tested subject was not falsey.') {
    if((bool)$subject) {
      throw new \Exception($message);
    }
  }

  /**
   * Attempts to find a button in a table row containing giving text.
   *
   * @Given /^I press "(?P<button>[^"]*)" in the "(?P<row_text>[^"]*)" row$/
   */
  public function assertPressButtonInTableRow($button, $row_text) {
    $page = $this->getSession()->getPage();
    $rows = $page->findAll('css', 'tr');
    if (!$rows) {
      throw new \Exception(sprintf('No rows found on the page %s', $this->getSession()->getCurrentUrl()));
    }
    $row_found = FALSE;
    foreach ($rows as $row) {
      if (strpos($row->getText(), $row_text) !== FALSE) {
        $row_found = TRUE;
        // Found text in this row, now find link in a cell.
        $cells = $row->findAll('css', 'td');
        if (!$cells) {
          throw new \Exception(sprintf('No cells found in table row on the page %s', $this->getSession()->getCurrentUrl()));
        }
        foreach ($cells as $cell) {
          $buttonObj = $cell->findButton($button);
          if (!empty($buttonObj)) {
            $cell->pressButton($button);
            return;
          }
        }
      }
    }
    if ($row_found) {
      throw new \Exception(sprintf('Found a row containing "%s", but no "%s" button on the page %s', $row_text, $button, $this->getSession()->getCurrentUrl()));
    }
    else {
      throw new \Exception(sprintf('Failed to find a row containing "%s" on the page %s', $row_text, $this->getSession()->getCurrentUrl()));
    }
  }

}
