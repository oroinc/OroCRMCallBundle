<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity;

use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\CallDirection;
use Oro\Bundle\CallBundle\Entity\CallStatus;
use Oro\Bundle\EntityExtendBundle\PropertyAccess;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;

class CallTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet(string $property, mixed $value)
    {
        $obj = new Call();

        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($obj, $property, $value);
        $this->assertSame($value, $accessor->getValue($obj, $property));
    }

    public function getSetDataProvider(): array
    {
        return [
            ['owner', $this->createMock(User::class)],
            ['subject', 'test'],
            ['phoneNumber', 'test'],
            ['notes', 'test'],
            ['callDateTime', new \DateTime()],
            ['callStatus', $this->createMock(CallStatus::class)],
            ['duration', 1],
            ['direction', $this->createMock(CallDirection::class)],
            ['organization', $this->createMock(Organization::class)],
        ];
    }

    public function testIsUpdatedFlags()
    {
        $date = new \DateTime('2012-12-12 12:12:12');
        $call = new Call();
        $call->setUpdatedAt($date);

        $this->assertTrue($call->isUpdatedAtSet());
    }

    public function testIsNotUpdatedFlags()
    {
        $call = new Call();
        $call->setUpdatedAt(null);

        $this->assertFalse($call->isUpdatedAtSet());
    }
}
