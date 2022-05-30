<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\ActivityContactBundle\Direction\DirectionProviderInterface;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\CallDirection;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\ContactBundle\Tests\Functional\DataFixtures\LoadContactEntitiesData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class ActivityListenerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient();
        $this->loadFixtures([LoadContactEntitiesData::class]);
    }

    public function testRemoveContactFromContextShouldDecreaseActivityCounter()
    {
        $firstContact = $this->findContact(LoadContactEntitiesData::FIRST_ENTITY_NAME);
        $secondContact = $this->findContact(LoadContactEntitiesData::SECOND_ENTITY_NAME);

        $firstContacted = $firstContact->getAcContactCount();
        $secondContacted = $firstContact->getAcContactCount();

        $call = new Call();
        $call
            ->setPhoneNumber('3058304958')
            ->setSubject('subj')
            ->setDirection($this->findCallDirection(DirectionProviderInterface::DIRECTION_OUTGOING));
        $this->getActivityManager()->addActivityTargets($call, [$firstContact, $secondContact]);
        $this->getEntityManager()->persist($call);
        $this->getEntityManager()->flush();

        $this->assertEquals($firstContacted + 1, $firstContact->getAcContactCount());
        $this->assertEquals($secondContacted + 1, $secondContact->getAcContactCount());

        $this->getActivityManager()->removeActivityTarget($call, $secondContact);
        $this->getEntityManager()->flush();

        $this->assertEquals($firstContacted + 1, $firstContact->getAcContactCount());
        $this->assertEquals($secondContacted, $secondContact->getAcContactCount());
    }

    private function getActivityManager(): ActivityManager
    {
        return $this->getContainer()->get('oro_activity.manager');
    }

    private function findCallDirection(string $name): CallDirection
    {
        return $this->getDoctrine()->getRepository(CallDirection::class)->findOneByName($name);
    }

    private function findContact(string $firstName): Contact
    {
        return $this->getDoctrine()->getRepository(Contact::class)->findOneByFirstName($firstName);
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->getDoctrine()->getManager();
    }

    private function getDoctrine(): ManagerRegistry
    {
        return self::getContainer()->get('doctrine');
    }
}
