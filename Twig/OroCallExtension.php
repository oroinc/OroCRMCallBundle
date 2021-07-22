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
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc }
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
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'oro_call.placeholder.log_call.filter' => LogCallPlaceholderFilter::class,
        ];
    }

    private function getLogCallPlaceholderFilter(): LogCallPlaceholderFilter
    {
        return $this->container->get('oro_call.placeholder.log_call.filter');
    }
}
