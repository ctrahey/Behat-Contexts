<?php
namespace MTM\Behat;
use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
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
    $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Service'));
    $loader->load('core.xml');
    $container->setParameter('mtm.behat.locations.config', $config['locations']);
    // Backwards compatibility for old style behat.local.yml config
    if (isset($config['email_box']) && !isset($config['email']['mailbox'])) {
      $config['email']['mailbox'] = $config['email_box'];
    }
    $container->setParameter('mtm.behat.email.email_box', $config['email']['mailbox']);
    if (!empty($config['email']['class']) && class_exists($config['email']['class'])) {
      $container->setParameter('mtm.email.class', $config['email']['class']);
    }
  }

  /**
   * Setups configuration for current extension.
   *
   * @param ArrayNodeDefinition $builder
   */
  public function getConfig(ArrayNodeDefinition $builder) {
    $builder->
        children()->
            arrayNode('locations')->
                prototype('variable')->end()->
            end()->
            scalarNode('email_box')->end()->
            arrayNode('email')->
                children()->
                    scalarNode('mailbox')->end()->
                    scalarNode('class')->end()->
                end()->
            end()->
        end()->
    end();
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
