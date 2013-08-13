<?php

namespace MTM\Behat\Context;

use Behat\Behat\Context\Initializer\InitializerInterface,
    Behat\Behat\Context\ContextInterface;

use MTM\Behat\Service\Locations,
    MTM\Behat\Service\LocationsConsumerInterface;

/**
 * MTM Extension contexts initializer.
 * Sets Location service on contexts that have a setLocations.
 *
 * @author Chris Trahey <christrahey@gmail.com>
 */
class Initializer implements \Behat\Behat\Context\Initializer\InitializerInterface
{
    private $locations;
    private $parameters;

    /**
     * Initializes initializer.
     *
     * @param Mink  $mink
     * @param array $parameters
     */
    public function __construct(Locations $locationService, array $parameters = array())
    {
        $this->locations = $locationService;
        $this->parameters = $parameters;
    }

    /**
     * Checks if initializer supports provided context.
     *
     * @param ContextInterface $context
     *
     * @return Boolean
     */
    public function supports(ContextInterface $context)
    {
        return ($context instanceof LocationsConsumerInterface);
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        $context->setLocations($this->locations);
    }
}
