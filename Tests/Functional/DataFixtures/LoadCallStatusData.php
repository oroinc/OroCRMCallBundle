<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CallBundle\Entity\CallStatus;

class LoadCallStatusData extends AbstractFixture
{
    public const CALL_STATUS_IN_PROGRESS = 'call_status_in_progress';
    public const CALL_STATUS_COMPLETED = 'call_status_completed';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository(CallStatus::class);
        /** @var CallStatus[] $statuses */
        $statuses = $repository->findBy(['name' => ['in_progress', 'completed']]);
        foreach ($statuses as $status) {
            $this->setReference('call_status_' . $status->getName(), $status);
        }
    }
}
