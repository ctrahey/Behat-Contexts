<?php

namespace MTM\Behat\Context;

use Behat\Behat\Context\Initializer\InitializerInterface,
    Behat\Behat\Context\ContextInterface;

use MTM\Behat\Service\Locations,
    MTM\Behat\Service\LocationsConsumerInterface;

use MTM\Behat\Service\Email,
    MTM\Behat\Service\EmailConsumerInterface;

/**
 * MTM Extension contexts initializer.
 * Sets Location service on contexts that have a setLocations.
 *
 * @author Chris Trahey <christrahey@gmail.com>
 */
class Initializer implements \Behat\Behat\Context\Initializer\InitializerInterface
{
    private $locations;
    private $mailbox;
    private $parameters;

    /**
     * Initializes initializer.
     *
     * @param Mink  $mink
     * @param array $parameters
     */
    public function __construct(Locations $locationService, Email $emailService, array $parameters = array())
    {
        $this->locations = $locationService;
        $this->mailbox = $emailService;
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
        $loc = ($context instanceof LocationsConsumerInterface);
        $email = ($context instanceof EmailConsumerInterface);
        $supports =  $loc || $email;
        return $supports;
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        if($context instanceof LocationsConsumerInterface) {
            $context->setLocations($this->locations);
        }
        if ($context instanceof EmailConsumerInterface) {
            $context->setEmail($this->mailbox);
        }
    }
}
