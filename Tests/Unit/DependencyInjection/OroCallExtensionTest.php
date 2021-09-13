<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\CallBundle\Controller\Api\Rest\CallController;
use Oro\Bundle\CallBundle\DependencyInjection\OroCallExtension;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;

class OroCallExtensionTest extends ExtensionTestCase
{
    public function testLoad(): void
    {
        $this->loadExtension(new OroCallExtension());

        $expectedDefinitions = [
            CallController::class,
        ];

        $this->assertDefinitionsLoaded($expectedDefinitions);
    }
}
