<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity;

use Oro\Bundle\CallBundle\Entity\CallStatus;
use PHPUnit\Framework\TestCase;

class CallStatusTest extends TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet(string $property, mixed $value, mixed $expected): void
    {
        $statusName = 'completed';
        $obj = new CallStatus($statusName);
        $this->assertEquals($statusName, $obj->getName());

        $obj->{'set' . ucfirst($property)}($value);
        $this->assertEquals($expected, call_user_func_array([$obj, 'get' . ucfirst($property)], []));
    }

    public function getSetDataProvider(): array
    {
        return [
            'label' => ['label', 'my status', 'my status'],
        ];
    }
}
