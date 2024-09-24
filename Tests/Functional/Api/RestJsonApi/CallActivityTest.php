<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ActivityBundle\EntityConfig\ActivityScope;
use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

class CallActivityTest extends RestJsonApiTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            '@OroCallBundle/Tests/Functional/Api/DataFixtures/call_data.yml'
        ]);
    }

    private function getActivityCallIds(int $contactId): array
    {
        $rows = $this->getEntityManager()->createQueryBuilder()
            ->from(Call::class, 't')
            ->select('t.id')
            ->join('t.' . ExtendHelper::buildAssociationName(Contact::class, ActivityScope::ASSOCIATION_KIND), 'c')
            ->where('c.id = :contactId')
            ->setParameter('contactId', $contactId)
            ->orderBy('t.id')
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'id');
    }

    public function testGet(): void
    {
        $response = $this->get(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'          => 'contacts',
                    'id'            => '<toString(@contact1->id)>',
                    'relationships' => [
                        'activityCalls' => [
                            'data' => [
                                ['type' => 'calls', 'id' => '<toString(@call1->id)>'],
                                ['type' => 'calls', 'id' => '<toString(@call2->id)>']
                            ]
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetSubresourceForActivityCalls(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>', 'association' => 'activityCalls']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => [
                            'subject' => '<toString(@call1->subject)>'
                        ]
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call2->id)>',
                        'attributes' => [
                            'subject' => '<toString(@call2->subject)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
    }

    public function testGetSubresourceForActivityCallsWithIncludeFilter(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>', 'association' => 'activityCalls'],
            ['include' => 'callStatus']
        );
        $this->assertResponseContains(
            [
                'data'     => [
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => [
                            'subject' => '<toString(@call1->subject)>'
                        ]
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call2->id)>',
                        'attributes' => [
                            'subject' => '<toString(@call2->subject)>'
                        ]
                    ]
                ],
                'included' => [
                    [
                        'type'       => 'callstatuses',
                        'id'         => '<toString(@call_status_completed->name)>',
                        'attributes' => [
                            'label' => '<toString(@call_status_completed->label)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
    }

    public function testGetRelationshipForActivityCalls(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'contacts', 'id' => '<toString(@contact1->id)>', 'association' => 'activityCalls']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    ['type' => 'calls', 'id' => '<toString(@call1->id)>'],
                    ['type' => 'calls', 'id' => '<toString(@call2->id)>']
                ]
            ],
            $response,
            true
        );
    }

    public function testUpdateRelationshipForActivityCalls(): void
    {
        $contactId = $this->getReference('contact2')->getId();
        $call2Id = $this->getReference('call2')->getId();
        $this->patchRelationship(
            ['entity' => 'contacts', 'id' => (string)$contactId, 'association' => 'activityCalls'],
            [
                'data' => [
                    ['type' => 'calls', 'id' => (string)$call2Id]
                ]
            ]
        );
        self::assertEquals([$call2Id], $this->getActivityCallIds($contactId));
    }

    public function testAddRelationshipForActivityCalls(): void
    {
        $contactId = $this->getReference('contact2')->getId();
        $call2Id = $this->getReference('call2')->getId();
        $call3Id = $this->getReference('call3')->getId();
        $this->postRelationship(
            ['entity' => 'contacts', 'id' => (string)$contactId, 'association' => 'activityCalls'],
            [
                'data' => [
                    ['type' => 'calls', 'id' => (string)$call3Id]
                ]
            ]
        );
        self::assertEquals([$call2Id, $call3Id], $this->getActivityCallIds($contactId));
    }

    public function testDeleteRelationshipForActivityCalls(): void
    {
        $contactId = $this->getReference('contact1')->getId();
        $call1Id = $this->getReference('call1')->getId();
        $call2Id = $this->getReference('call2')->getId();
        $this->deleteRelationship(
            ['entity' => 'contacts', 'id' => (string)$contactId, 'association' => 'activityCalls'],
            [
                'data' => [
                    ['type' => 'calls', 'id' => (string)$call1Id]
                ]
            ]
        );
        self::assertEquals([$call2Id], $this->getActivityCallIds($contactId));
    }
}
