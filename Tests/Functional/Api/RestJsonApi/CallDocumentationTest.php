<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Request\ApiAction;
use Oro\Bundle\ApiBundle\Tests\Functional\DocumentationTestTrait;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;

/**
 * @group regression
 */
class CallDocumentationTest extends RestJsonApiTestCase
{
    use DocumentationTestTrait;

    /** @var string used in DocumentationTestTrait */
    private const VIEW = 'rest_json_api';

    private static bool $isDocumentationCacheWarmedUp = false;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        if (!self::$isDocumentationCacheWarmedUp) {
            $this->warmUpDocumentationCache();
            self::$isDocumentationCacheWarmedUp = true;
        }
    }

    public function testCallActivityTargets(): void
    {
        $docs = $this->getEntityDocsForAction('calls', ApiAction::GET);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>Records associated with the call record.</p>',
            $resourceData['response']['activityTargets']['description']
        );
    }

    public function testTargetEntityActivityCalls(): void
    {
        $docs = $this->getEntityDocsForAction('contacts', ApiAction::GET);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals(
            '<p>The calls associated with the contact record.</p>',
            $resourceData['response']['activityCalls']['description']
        );
    }

    public function testCallActivityTargetsGetSubresource(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('calls', 'activityTargets', ApiAction::GET_SUBRESOURCE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get activity targets', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve records associated with a specific call record.</p>',
            $resourceData['documentation']
        );
    }

    public function testCallActivityTargetsGetRelationship(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('calls', 'activityTargets', ApiAction::GET_RELATIONSHIP);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get "activity targets" relationship', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the IDs of records associated with a specific call record.</p>',
            $resourceData['documentation']
        );
    }

    public function testTargetEntityActivityCallsGetSubresource(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('contacts', 'activityCalls', ApiAction::GET_SUBRESOURCE);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get activity calls', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve records of the calls associated with a specific contact record.</p>',
            $resourceData['documentation']
        );
    }

    public function testTargetEntityActivityCallsGetRelationship(): void
    {
        $docs = $this->getSubresourceEntityDocsForAction('contacts', 'activityCalls', ApiAction::GET_RELATIONSHIP);
        $resourceData = $this->getResourceData($this->getSimpleFormatter()->format($docs));
        self::assertEquals('Get "activity calls" relationship', $resourceData['description']);
        self::assertEquals(
            '<p>Retrieve the IDs of the calls associated with a specific contact record.</p>',
            $resourceData['documentation']
        );
    }
}
