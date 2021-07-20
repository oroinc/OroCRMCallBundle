<?php

namespace Oro\Bundle\CallBundle\Twig;

use Oro\Bundle\CallBundle\Placeholder\LogCallPlaceholderFilter;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides a Twig function to check if call log is available for an entity:
 *   - isCallLogApplicable
 */
class OroCallExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    protected $container;

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
            new TwigFunction('isCallLogApplicable', [$this, 'isCallLogApplicable']),
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

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'oro_call.placeholder.log_call.filter' => LogCallPlaceholderFilter::class,
        ];
    }
}
