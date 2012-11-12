<?php
namespace MTM\Behat\Compiler;
use Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * A Symfony Service Container CompilerPass.
 * When properly registered, ->process() will be 
 * called during the compilation of Behat's Dependency
 * Injection Service Container. 
 *
 * Our task here is to configure the container to 
 * pass itself to any Context objects it creates.
 */
class SubContextReaderPass implements CompilerPassInterface {
  public function process(ContainerBuilder $container) {
    $container->setParameter('behat.context.parameters', array('service_container' => $container));
  }
}