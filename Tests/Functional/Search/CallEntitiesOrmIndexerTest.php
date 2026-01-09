<?php

declare(strict_types=1);

namespace Oro\Bundle\CallBundle\Tests\Functional\Search;

use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\CallDirection;
use Oro\Bundle\CallBundle\Entity\CallStatus;
use Oro\Bundle\CallBundle\Tests\Functional\DataFixtures\LoadCallDirectionData;
use Oro\Bundle\CallBundle\Tests\Functional\DataFixtures\LoadCallStatusData;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SearchBundle\Tests\Functional\Engine\AbstractEntitiesOrmIndexerTest;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadOrganization;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadUser;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Tests that Call entities can be indexed without type casting errors with the ORM search engine.
 *
 * @group search
 * @dbIsolationPerTest
 */
class CallEntitiesOrmIndexerTest extends AbstractEntitiesOrmIndexerTest
{
    #[\Override]
    protected function getSearchableEntityClassesToTest(): array
    {
        return [Call::class];
    }

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            LoadOrganization::class,
            LoadUser::class,
            LoadCallDirectionData::class,
            LoadCallStatusData::class,
        ]);

        $manager = $this->getDoctrine()->getManagerForClass(Call::class);
        /** @var Organization $organization */
        $organization = $this->getReference(LoadOrganization::ORGANIZATION);
        /** @var User $owner */
        $owner = $this->getReference(LoadUser::USER);
        /** @var CallDirection $direction */
        $direction = $this->getReference(LoadCallDirectionData::CALL_DIRECTION_INCOMING);
        /** @var CallStatus $status */
        $status = $this->getReference(LoadCallStatusData::CALL_STATUS_COMPLETED);

        $call = (new Call())
            ->setSubject('Test Call')
            ->setPhoneNumber('123-456-7890')
            ->setNotes('Test call notes')
            ->setOrganization($organization)
            ->setOwner($owner)
            ->setDirection($direction)
            ->setCallStatus($status)
            ->setCallDateTime(new \DateTime('now', new \DateTimeZone('UTC')));
        $this->persistTestEntity($call);

        $manager->flush();
    }
}
