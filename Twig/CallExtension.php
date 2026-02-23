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
class CallExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    #[\Override]
    public function getFunctions()
    {
        return [
            new TwigFunction('isCallLogApplicable', [$this, 'isCallLogApplicable']),
        ];
    }

    public function isCallLogApplicable(?object $entity): bool
    {
        return $this->getLogCallPlaceholderFilter()->isApplicable($entity);
    }

    #[\Override]
    public static function getSubscribedServices(): array
    {
        return [
            LogCallPlaceholderFilter::class
        ];
    }

    private function getLogCallPlaceholderFilter(): LogCallPlaceholderFilter
    {
        return $this->container->get(LogCallPlaceholderFilter::class);
    }
}
