<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity\Manager;

use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\Manager\CallActivityManager;
use Oro\Bundle\CallBundle\Tests\Unit\Fixtures\Entity\TestTarget;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CallActivityManagerTest extends TestCase
{
    private ActivityManager&MockObject $activityManager;
    private CallActivityManager $manager;

    #[\Override]
    protected function setUp(): void
    {
        $this->activityManager = $this->createMock(ActivityManager::class);

        $this->manager = new CallActivityManager($this->activityManager);
    }

    public function testAddAssociation(): void
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
