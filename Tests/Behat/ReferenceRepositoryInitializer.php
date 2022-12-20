<?php

namespace Oro\Bundle\CallBundle\Tests\Behat;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\CallBundle\Entity\CallDirection;
use Oro\Bundle\CallBundle\Entity\CallStatus;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\ReferenceRepositoryInitializerInterface;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\Collection;

class ReferenceRepositoryInitializer implements ReferenceRepositoryInitializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function init(ManagerRegistry $doctrine, Collection $referenceRepository): void
    {
        $callStatusRepository = $doctrine->getRepository(CallStatus::class);
        $referenceRepository->set('call_in_progress', $callStatusRepository->findOneByName('in_progress'));
        $referenceRepository->set('call_completed', $callStatusRepository->findOneByName('completed'));

        $callDirectionRepository = $doctrine->getRepository(CallDirection::class);
        $referenceRepository->set('call_incoming', $callDirectionRepository->findOneByName('incoming'));
        $referenceRepository->set('call_outgoing', $callDirectionRepository->findOneByName('outgoing'));
    }
}
