<?php

namespace Oro\Bundle\CallBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ActivityListBundle\Migrations\Data\ORM\AddActivityListsData;
use Oro\Bundle\CallBundle\Entity\Call;

/**
 * Adds activity lists for Call entity.
 */
class AddCallsActivityLists extends AddActivityListsData implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [UpdateCallWithOrganization::class];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $this->addActivityListsForActivityClass(
            $manager,
            Call::class,
            'owner',
            'organization'
        );
    }
}
