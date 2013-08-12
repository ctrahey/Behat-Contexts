<?php
namespace MTM\Behat;

/**
 * Multi-Persona context.
 * With the \MTM\Behat\Context system of tag-based sub-contexts,
 * just tag your scenario with @persona. Add a step:
 *
 * Given I adopt the persona "Sally"
 *
 * and all subsequent steps (until the next persona switch) will operate
 * in a unique browsing context named "Sally".
 */
class MultiPersonaContext extends SubContext
{
  /**
   * @Given /^I adopt the persona (.*)$/
   */
  public function changePersona($name) {
    $mink = $this->getMink();
    if(!$mink->hasSession($name)) {
        $newSession = clone $this->getSession();
        $mink->registerSession($name, $newSession);
      }
    $mink->setDefaultSessionName($name);
  }
}