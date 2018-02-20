<?php

namespace Oro\Bundle\CallBundle\Twig;

use Oro\Bundle\CallBundle\Placeholder\LogCallPlaceholderFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OroCallExtension extends \Twig_Extension
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return LogCallPlaceholderFilter
     */
    protected function getLogCallPlaceholderFilter()
    {
        return $this->container->get('oro_call.placeholder.log_call.filter');
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('isCallLogApplicable', [$this, 'isCallLogApplicable']),
        ];
    }

    /**
     * @param object|null $entity
     *
     * @return bool
     */
    public function isCallLogApplicable($entity)
    {
        return $this->getLogCallPlaceholderFilter()->isApplicable($entity);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'oro_call_extension';
    }
}
