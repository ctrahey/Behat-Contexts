<?php
namespace MTM\Behat;

/**
 * Misc context.
 */
class MiscContext extends \Behat\Behat\Context\BehatContext
{
  public function __call($method, $args) {
    if(method_exists($this->getMainContext(), $method)) {
      return call_user_func_array(array($this->getMainContext(), $method), $args);
    }
    return parent::__call($method, $args);
  }
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
   * Assert selector is hidden.
   *
   * @Then /^(?:|I )should not be able to see "(?P<element>[^"]*)"$/
   */
  public function assertHiddenOnPage($selector)
  {
    assertTrue($this->getSession()->evaluateScript("return jQuery('" . $selector . "').is(':hidden')"));        
  }
  /**
   * Assert selector is visible.
   *
   * @Then /^(?:|I )should be able to see "(?P<element>[^"]*)"$/
   */
  public function assertVisibleOnPage($selector)
  {
    assertTrue($this->getSession()->evaluateScript("return jQuery('" . $selector . "').is(':visible')"));        
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
   * @Given /^I wait for "([^"]*)" second$/   
   * @Given /^I wait for "([^"]*)" seconds$/   
   */
  public function iWaitForSeconds($sec) {
    $this->getSession()->wait((int)$sec * 1000);
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

}