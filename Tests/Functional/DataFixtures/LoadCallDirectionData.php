<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CallBundle\Entity\CallDirection;

class LoadCallDirectionData extends AbstractFixture
{
    public const CALL_DIRECTION_INCOMING = 'call_direction_incoming';
    public const CALL_DIRECTION_OUTGOING = 'call_direction_outgoing';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository(CallDirection::class);
        /** @var CallDirection[] $directions */
        $directions = $repository->findBy(['name' => ['incoming', 'outgoing']]);
        foreach ($directions as $direction) {
            $this->setReference('call_direction_' . $direction->getName(), $direction);
        }
    }
}
