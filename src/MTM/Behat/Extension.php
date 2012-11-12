<?php
namespace MTM\Behat;
use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @class Extension
 * A Behat Extension to facilitate more robust SubContexts
 * This object's only task (circa Nov 11, 2012) is to attach
 * a ServiceContainer CompilerPass object:
 * @see Compiler\SubContextReaderPass()
 */
class Extension implements ExtensionInterface {
  /**
   * Loads a specific configuration.
   *
   * @param array            $config    Extension configuration hash (from behat.yml)
   * @param ContainerBuilder $container ContainerBuilder instance
   */
  public function load(array $config, ContainerBuilder $container) {

  }

  /**
   * Setups configuration for current extension.
   *
   * @param ArrayNodeDefinition $builder
   */
  public function getConfig(ArrayNodeDefinition $builder) {
  }

  /**
   * Returns compiler passes used by this extension.
   *
   * @return array
   */
  public function getCompilerPasses() {
    return array(
      new Compiler\SubContextReaderPass(),
    );
  }
  
}