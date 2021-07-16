<?php

namespace Oro\Bundle\CallBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Oro\Bundle\CallBundle\Entity\Manager\CallActivityManager;

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
