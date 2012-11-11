<?php
namespace MTM\Behat;
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';


class Context extends \Drupal\DrupalExtension\Context\DrupalContext {
  /**
   * @property array $tagsConfig Map tags to subcontexts
   */
  protected $tagsConfig = NULL;
  
  /**
   * @beforeScenario
   */
  public function attachEmailContext($event) {
    $this->useContext('misc', new MiscContext());
    if($event instanceof \Behat\Behat\Event\OutlineEvent) {
      $node = $event->getOutline();
    } elseif ($event instanceof \Behat\Behat\Event\ScenarioEvent) {
      $node = $event->getScenario();
    } else {
      return;
    }
    $tags = $node->getTags();
    foreach($tags as $tag) {
      $this->attachContextsForTag($tag);      
    }
  }
  
  public function getTagsConfiguration() {
    if(empty($this->tagsConfig)) {
      ini_set('display_errors', TRUE);
      $parser = new \Symfony\Component\Yaml\Parser();
      $this->tagsConfig = $parser->parse(file_get_contents(dirname(__FILE__) . '/ContextTags.yml'));
    }
    return $this->tagsConfig;
  }
  
  public function attachContextsForTag($tag) {
    $config = $this->getTagsConfiguration();
    if(!array_key_exists($tag, $config)) {
      return;
    }
    foreach($config[$tag] as $className) {
      $className = __NAMESPACE__ . '\\' . $className;
      $existing = $this->getSubcontextByClassName($className);
      if(!$existing) {
        $subCtx = new $className();
        $this->useContext($className, $subCtx);
      }
    }
  }
  
}