<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity;

use Oro\Bundle\CallBundle\Entity\Call;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CallTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value)
    {
        $obj = new Call();

        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($obj, $property, $value);
        $this->assertSame($value, $accessor->getValue($obj, $property));
    }

    public function getSetDataProvider()
    {
        return array(
            array('owner', $this->createMock('Oro\Bundle\UserBundle\Entity\User')),
            array('subject', 'test'),
            array('phoneNumber', 'test'),
            array('notes', 'test'),
            array('callDateTime', new \DateTime()),
            array(
                'callStatus',
                $this->getMockBuilder('Oro\Bundle\CallBundle\Entity\CallStatus')
                    ->disableOriginalConstructor()
                    ->getMock()
            ),
            array('duration', 1),
            array(
                'direction',
                $this->getMockBuilder('Oro\Bundle\CallBundle\Entity\CallDirection')
                    ->disableOriginalConstructor()
                    ->getMock()

            ),
            array('organization', $this->createMock('Oro\Bundle\OrganizationBundle\Entity\Organization')),
        );
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
