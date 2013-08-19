<?php
namespace MTM\Behat;
use Behat\Behat\Context\BehatContext;

/**
 * Metal Toad's core Context class.
 * All FeatureContext classes should directly extend this.
 */
class Context extends BehatContext implements Service\LocationsConsumerInterface{
  /**
   * @property string name of user-configured default driver session.
   */
  protected $defaultSessionName = NULL;

  /**
   * @property array $tagsConfig Map tags to subcontexts
   */
  protected $tagsConfig = NULL;

  /**
   * A map from a __call() method name to
   * a subcontext where it is implemented.
   */
  protected $methodSubContextMap = array();

  /**
   * Dependency Injection ServiceContainer, all of Behat's Services
   */
  protected $container = NULL;

  /**
   * @property Service\Locations
   */
  protected $locationsService = NULL;

  public function __construct(array $params) {
    $this->container = $params['service_container'];
  }

  public function setLocations(Service\Locations $locations) {
    $this->locationsService = $locations;
  }

  /**
  * @Given /^I (?:am on|visit) (?:the|a) ([^"]*)[ ]*(?:page|a)$/
   */
  public function gotToNamedPath($pathName) {
    return $this->visit($this->locationsService->resolve($pathName));
  }

  /**
   * Initialize subcontexts just like they deserve :-)
   * This allows subcontexts to be added after __construct()
   * and still get their full init calls.
   */
  public function useContext($alias, $object) {
    parent::useContext($alias, $object);
    if(empty($this->container)) {
      throw new \Exception('No Service Container configured on Context. Did you forget to configure MTM Extension? Or perhaps you have a Context __construct which is not calling parent::__construct()?');
    }
    $reader = $this->container->get('behat.context.reader');
    $dispatcher = $this->container->get('behat.context.dispatcher');
    $dispatcher->initializeContext($object);
    $reader->readFromContext($object);
  }

  /**
   * Find tags applied to this class in it's docblock
   * Format (in the docblock above the class):
   * @tags foo, bar, baz
   */
  public function getClassTags() {
    $tags = array();
    $refl = new \ReflectionClass(get_called_class());
    $matches = array();
    $comment = $refl->getDocComment();
    preg_match('/@tags (.+)/', $comment, $matches);
    if(count($matches) > 1) {
      $tags = array_map('trim', explode(',', $matches[1]));
    }
    return $tags;
  }

  /**
   * @afterScenario
   */
  public function restoreDefaultSession() {
    $mink = $this->getMink();
    $mink->setDefaultSessionName($this->defaultSessionName);
  }

   public function detectPreferredDriver($event) {
     $mink = $this->getMink();
     if(!$this->defaultSessionName) {
       $this->defaultSessionName = $mink->getDefaultSessionName();
     }
     if($event instanceof \Behat\Behat\Event\OutlineEvent) {
       $node = $event->getOutline();
     } elseif ($event instanceof \Behat\Behat\Event\ScenarioEvent) {
       $node = $event->getScenario();
     } else {
       return;
     }
     $tags = $node->getTags();
     foreach($tags as $tagName) {
       $index = strpos($tagName, 'Driver');
       if(FALSE !== $index) {
         $driverName = substr($tagName, 0, $index);
         if(!$mink->hasSession($driverName)) {
           $driverClass = '\\Behat\\Mink\\Driver\\' . $tagName;
           $mink->registerSession($driverName, new $driverClass);
         }
         $mink->setDefaultSessionName($driverName);
       }
     }
   }

  /**
   * @beforeScenario
   */
  public function attachScenarioContexts($event) {
    $this->useContext('misc', new MiscContext());
    if($event instanceof \Behat\Behat\Event\OutlineEvent) {
      $node = $event->getOutline();
    } elseif ($event instanceof \Behat\Behat\Event\ScenarioEvent) {
      $node = $event->getScenario();
    } else {
      return;
    }
    $tags = $node->getTags();
    // grab any tags declared at the class level
    $tags = array_merge($tags, $this->getClassTags());
    foreach($tags as $tag) {
      $this->attachContextsForTag($tag);
    }
    $this->detectPreferredDriver($event);
  }

  /**
   * try to find exactly one subcontext with that method
   * this allows us to use subcontexts similar to parent classes
   */
  public function __call($method, $args) {
    if(!array_key_exists($method, $this->methodSubContextMap)) {
      $found = false;
      foreach($this->getSubcontexts() as $subcontext) {
        if(method_exists($subcontext, $method)) {
          $className = get_class($subcontext);
          if($found) {
            $previous = $this->methodSubContextMap[$method];
            $errString = 'Call to ambiguous method (%s). Found in subcontext "%s" and "%s"';
            throw new \Exception(sprintf($errString, $method, $previous, $className));
          }
          $found = true;
          $this->methodSubContextMap[$method] = $className;
        }
      }
    }
    if(array_key_exists($method, $this->methodSubContextMap)) {
      $subContextClass = $this->methodSubContextMap[$method];
      $subContext = $this->getSubcontextByClassName($subContextClass);
      return call_user_func_array(array($subContext, $method), $args);
    } else {
      throw new \Exception('Cannot locate method "' . $method . '". This may occur due to a missing tag (@drupal is common)');
    }
  }

  public function __get($propName) {
    foreach($this->getSubcontexts() as $subcontext) {
      if(property_exists($subcontext, $propName)) {
        return $subcontext->$propName;
      }
    }
  }

  public function __set($propName, $val) {
    foreach($this->getSubcontexts() as $subcontext) {
      if(property_exists($subcontext, $propName)) {
        $subcontext->$propName = $val;
        return;
      }
    }
    $this->$propName = $val;
  }

  /**
   * Load mapping of tag to subcontext class.
   */
  public function getTagsConfiguration() {
    if(empty($this->tagsConfig)) {
      ini_set('display_errors', TRUE);
      $parser = new \Symfony\Component\Yaml\Parser();
      $this->tagsConfig = $parser->parse(file_get_contents(dirname(__FILE__) . '/ContextTags.yml'));
    }
    return $this->tagsConfig;
  }

  /**
   * Using tags configuration, instantiate and
   * attach the appropriate subcontexts for this tag
   */
  public function attachContextsForTag($tag) {
    $config = $this->getTagsConfiguration();
    if(!array_key_exists($tag, $config)) {
      return;
    }
    foreach($config[$tag] as $className) {
      if(!class_exists($className, TRUE)) {
        // allows for fully-qualified names in our config file,
        // and if not found, assume our local namespace.
        $className = __NAMESPACE__ . '\\' . $className;
      }
      $existing = $this->getSubcontextByClassName($className);
      if(!$existing) {
        $subCtx = new $className();
        $this->useContext($className, $subCtx);
      }
    }
  }

}
