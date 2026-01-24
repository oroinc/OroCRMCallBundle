<?php

namespace Oro\Bundle\CallBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Oro\Bundle\CallBundle\Entity\Manager\CallActivityManager;

/**
 * Handles call entity lifecycle events by delegating to {@see CallActivityManager}
 * to manage call activity associations during flush operations.
 */
class EntityListener
{
    /** @var CallActivityManager */
    protected $callActivityManager;

    public function __construct(CallActivityManager $callActivityManager)
    {
        $this->callActivityManager = $callActivityManager;
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $this->callActivityManager->handleOnFlush($event);
    }
}
