<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity;

use Oro\Bundle\CallBundle\Entity\CallDirection;
use PHPUnit\Framework\TestCase;

class CallDirectionTest extends TestCase
{
    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet(string $property, mixed $value, mixed $expected): void
    {
        $directionName = 'forward';
        $obj = new CallDirection($directionName);
        $this->assertEquals($directionName, $obj->getName());

        $obj->{'set' . ucfirst($property)}($value);
        $this->assertEquals($expected, call_user_func_array([$obj, 'get' . ucfirst($property)], []));
    }

    public function getSetDataProvider(): array
    {
        return [
            'label' => ['label', 'my direction', 'my direction'],
        ];
    }
}
