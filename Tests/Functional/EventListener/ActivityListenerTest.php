<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\EventListener;

use Doctrine\ORM\EntityManagerInterface;
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
        /** @var ActivityManager $activityManager */
        $activityManager = self::getContainer()->get('oro_activity.manager');
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine')->getManagerForClass(Call::class);

        /** @var Contact $firstContact */
        $firstContact = $em->getRepository(Contact::class)->findOneBy([
            'firstName' => LoadContactEntitiesData::FIRST_ENTITY_NAME
        ]);
        /** @var Contact $secondContact */
        $secondContact = $em->getRepository(Contact::class)->findOneBy([
            'firstName' => LoadContactEntitiesData::SECOND_ENTITY_NAME
        ]);

        $firstContacted = $firstContact->getAcContactCount();
        $secondContacted = $firstContact->getAcContactCount();

        $call = new Call();
        $call->setPhoneNumber('3058304958');
        $call->setSubject('subj');
        $call->setDirection($em->getRepository(CallDirection::class)->findOneBy([
            'name' => DirectionProviderInterface::DIRECTION_OUTGOING
        ]));
        $activityManager->addActivityTargets($call, [$firstContact, $secondContact]);
        $em->persist($call);
        $em->flush();

        $this->assertEquals($firstContacted + 1, $firstContact->getAcContactCount());
        $this->assertEquals($secondContacted + 1, $secondContact->getAcContactCount());

        $activityManager->removeActivityTarget($call, $secondContact);
        $em->flush();

        $this->assertEquals($firstContacted + 1, $firstContact->getAcContactCount());
        $this->assertEquals($secondContacted, $secondContact->getAcContactCount());
    }
}
