<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity\Manager;

use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\Manager\CallActivityManager;
use Oro\Bundle\CallBundle\Tests\Unit\Fixtures\Entity\TestTarget;

class CallActivityManagerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ActivityManager|\PHPUnit\Framework\MockObject\MockObject */
    private $activityManager;

    /** @var CallActivityManager */
    private $manager;

    protected function setUp(): void
    {
        $this->activityManager = $this->createMock(ActivityManager::class);

        $this->manager = new CallActivityManager($this->activityManager);
    }

    public function testAddAssociation()
    {
        $call = new Call();
        $target = new TestTarget();

        $this->activityManager->expects($this->once())
            ->method('addActivityTarget')
            ->with($this->identicalTo($call), $this->identicalTo($target))
            ->willReturn(true);

        $this->assertTrue(
            $this->manager->addAssociation($call, $target)
        );
    }
}
