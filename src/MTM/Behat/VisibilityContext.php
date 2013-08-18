<?php
namespace MTM\Behat;

/**
 * Visibility context.
 * Provides steps and functionality for working with the 
 * visibility of elements in the current page.
 */
class VisibilityContext extends SubContext
{
  
  public function attachVisibilityTools() {
    $filterScript = file_get_contents(dirname(__FILE__) . '/visibilityTools.js');
    $this->getSession()->evaluateScript($filterScript);
  }
  public function opForCompare($comparison) {
    if(strlen(trim($comparison)) == 0) {
      $comparison = 'exactly';
    }
    $comparatorMap = array(
      'exactly' => '==',
      'at least' => '>=',
      'at most' => '<=',
    );
    return $comparatorMap[$comparison];
  }
  public function filterForQualifier($qualifier) {
    $filterMap = array(
      'visible' => 'actuallyVisible',
      'partially obscured' => 'partiallyClipped',
      'partially-obscured' => 'partiallyClipped',
    );
    return $filterMap[$qualifier];
  }

  /**
   * @Then /^I should see (exactly|at least|at most)? *(\d+) ([^"0-9]*) "([^"]*)" elements? in the "([^"]*)" area$/
   */
  public function iShouldSeeQtyElementsInTheArea($comparator, $quantity, $qualifier, $elementSelector, $areaName) {
    $this->attachVisibilityTools();    
    $comparisonOp = $this->opForCompare($comparator);
    $filter = $this->filterForQualifier($qualifier);
    $area = $this->selectorForAlias($areaName);
    $script = "return (jQuery('$area').find('$elementSelector').filter(':$filter').length) $comparisonOp $quantity";
    $this->assertTrue($this->getSession()->evaluateScript($script), "Expected to find $quantity $qualifier $elementSelector elements in $areaName");
  }

  /**
   * Assert selector is hidden.
   *
   * @Then /^(?:|I )should not be able to see "(?P<element>[^"]*)"$/
   */
  public function assertHiddenOnPage($selector)
  {
    $test = "return jQuery('" . $selector . "').is(':hidden')";
    $this->assertTrue($this->getSession()->evaluateScript("return jQuery('" . $selector . "').is(':hidden')"), "$selector was found on the page");
  }
  /**
   * Assert selector is visible.
   *
   * @Then /^(?:|I )should be able to see "(?P<element>[^"]*)"$/
   */
  public function assertVisibleOnPage($selector)
  {
    $test = "return jQuery('" . $selector . "').is(':actuallyVisible')";
    $this->assertTrue($this->getSession()->evaluateScript($test), "$selector was not visible on the page");
  }

  /**
   * @Then /^I should (not)? ?see the image "([^"]*)"$/
   */
  public function iShouldSeeTheImage($not, $image) {
    $this->attachVisibilityTools();
    $imageFile = $this->imageForAlias($image);
    $compare = ($not == 'not') ? '==' : '>';
    $script = <<<SCRIPT
    return jQuery('*').styleMatch('background-image', '$imageFile').filter(':actuallyVisible').length $compare 0;
SCRIPT;
    $this->assertTrue($this->getSession()->evaluateScript($script), "Expected to $not find '$image' image");
  }


}