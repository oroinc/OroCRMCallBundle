<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Tests\Functional\DataFixtures\LoadCallDirectionData;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\UserProBundle\Tests\Functional\DataFixtures\LoadOrganizationData;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class CallTest extends RestJsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $fixtures = [
            '@OroCallBundle/Tests/Functional/Api/DataFixtures/call_data.yml'
        ];
        // load several organizations for EE platform
        if (class_exists(LoadOrganizationData::class)) {
            $fixtures[] = LoadOrganizationData::class;
        }
        $this->loadFixtures($fixtures);
    }

    private function getActivityTargetIds(Call $call, string $targetClass): array
    {
        $result = [];
        $targets = $call->getActivityTargets($targetClass);
        foreach ($targets as $target) {
            $result[] = $target->getId();
        }
        sort($result);

        return $result;
    }

    public function testGetList(): void
    {
        $response = $this->cget(['entity' => 'calls']);
        $this->assertResponseContains('cget_call.yml', $response);
    }

    public function testGet(): void
    {
        $response = $this->get(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>']
        );
        $this->assertResponseContains('get_call.yml', $response);
    }

    public function testCreate(): void
    {
        $contact1Id = $this->getReference('contact1')->getId();

        $response = $this->post(
            ['entity' => 'calls'],
            [
                'data' => [
                    'type'          => 'calls',
                    'attributes'    => [
                        'subject'     => 'Subject of test call',
                        'phoneNumber' => '123-123',
                    ],
                    'relationships' => [
                        'direction'       => [
                            'data' => ['type' => 'calldirections', 'id' => '<toString(@call_direction_incoming->name)>']
                        ],
                        'callStatus'      => [
                            'data' => ['type' => 'callstatuses', 'id' => '<toString(@call_status_in_progress->name)>']
                        ],
                        'activityTargets' => [
                            'data' => [
                                ['type' => 'contacts', 'id' => '<toString(@contact1->id)>']
                            ]
                        ]
                    ]
                ]
            ]
        );

        $callId = (int)$this->getResourceId($response);
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertEquals('Subject of test call', $call->getSubject());
        self::assertEquals('123-123', $call->getPhoneNumber());
        self::assertNull($call->getNotes());
        self::assertNotNull($call->getCallDateTime());
        self::assertEquals('Incoming', $call->getDirection()->getLabel());
        self::assertEquals('in_progress', $call->getCallStatus()->getName());
        self::assertEquals([$contact1Id], $this->getActivityTargetIds($call, Contact::class));
    }

    public function testUpdate(): void
    {
        $callId = $this->getReference('call1')->getId();
        $data = [
            'data' => [
                'type'          => 'calls',
                'id'            => (string)$callId,
                'attributes'    => [
                    'subject'      => 'New subject of test call',
                    'notes'        => 'New notes of test call',
                    'callDateTime' => '2036-02-16T22:36:37Z'
                ],
                'relationships' => [
                    'direction'       => [
                        'data' => ['type' => 'calldirections', 'id' => '<toString(@call_direction_incoming->name)>']
                    ],
                    'activityTargets' => [
                        'data' => [
                            ['type' => 'contacts', 'id' => '<toString(@contact1->id)>']
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->patch(
            ['entity' => 'calls', 'id' => (string)$callId],
            $data
        );
        $this->assertResponseContains($data, $response);

        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertEquals('New subject of test call', $call->getSubject());
        self::assertEquals('New notes of test call', $call->getNotes());
        self::assertEquals(new \DateTime('2036-02-16T22:36:37Z'), $call->getCallDateTime());
        self::assertEquals('Incoming', $call->getDirection()->getLabel());
        self::assertEquals('completed', $call->getCallStatus()->getName());
    }

    public function testGetSubresourceForCallStatus(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'callStatus']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'callstatuses',
                    'id'         => '<toString(@call_status_completed->name)>',
                    'attributes' => [
                        'label' => '<toString(@call_status_completed->label)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForCallStatus(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'callStatus']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'callstatuses',
                    'id'   => '<toString(@call_status_completed->name)>'
                ]
            ],
            $response
        );
    }

    public function testUpdateRelationshipForCallStatus(): void
    {
        $callId = $this->getReference('call1')->getId();
        $response = $this->patchRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'callStatus'],
            [
                'data' => [
                    'type' => 'callstatuses',
                    'id'   => '<toString(@call_status_in_progress->name)>'
                ]
            ]
        );
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertSame('in_progress', $call->getCallStatus()->getName());
    }

    public function testGetSubresourceForOwner(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'owner']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'users',
                    'id'         => '<toString(@call1->owner->id)>',
                    'attributes' => [
                        'username' => '<toString(@call1->owner->username)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForOwner(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'owner']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'users',
                    'id'   => '<toString(@call1->owner->id)>'
                ]
            ],
            $response
        );
    }

    public function testUpdateRelationshipForOwner(): void
    {
        $callId = $this->getReference('call1')->getId();
        $ownerId = $this->getReference('user2')->getId();
        $this->patchRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'owner'],
            [
                'data' => [
                    'type' => 'users',
                    'id'   => (string)$ownerId
                ]
            ]
        );
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertSame($ownerId, $call->getOwner()->getId());
    }

    public function testGetSubresourceForOrganization(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'organization']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'organizations',
                    'id'         => '<toString(@call1->organization->id)>',
                    'attributes' => [
                        'name' => '<toString(@call1->organization->name)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForOrganization(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'organization']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'organizations',
                    'id'   => '<toString(@call1->organization->id)>'
                ]
            ],
            $response
        );
    }

    public function testTryToUpdateRelationshipForOrganization(): void
    {
        if (!class_exists(LoadOrganizationData::class)) {
            $this->markTestSkipped('EE platform is required');
        }

        $callId = $this->getReference('call1')->getId();
        $response = $this->patchRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'organization'],
            [
                'data' => [
                    'type' => 'organizations',
                    'id'   => '<toString(@organization_1->id)>'
                ]
            ],
            [],
            false
        );
        $this->assertResponseContainsValidationError(
            [
                'title'  => 'organization constraint',
                'detail' => 'You have no access to set this value as organization.'
            ],
            $response
        );
        $this->assertResponseContainsValidationError(
            [
                'title'  => 'access granted constraint',
                'detail' => 'The "VIEW" permission is denied for the related resource.'
            ],
            $response
        );
    }

    public function testGetSubresourceForDirection(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'direction']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'calldirections',
                    'id'         => '<toString(@call_direction_incoming->name)>',
                    'attributes' => [
                        'label' => '<toString(@call_direction_incoming->label)>'
                    ]
                ]
            ],
            $response
        );
    }

    public function testGetRelationshipForDirection(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'calls', 'id' => '<toString(@call1->id)>', 'association' => 'direction']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    'type' => 'calldirections',
                    'id'   => '<toString(@call_direction_incoming->name)>'
                ]
            ],
            $response
        );
    }

    public function testUpdateRelationshipForDirection(): void
    {
        $callId = $this->getReference('call1')->getId();
        $priorityName = $this->getReference(LoadCallDirectionData::CALL_DIRECTION_INCOMING)->getName();
        $this->patchRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'direction'],
            [
                'data' => [
                    'type' => 'calldirections',
                    'id'   => $priorityName
                ]
            ]
        );
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertSame($priorityName, $call->getDirection()->getName());
    }

    public function testGetSubresourceForActivityTargets(): void
    {
        $response = $this->getSubresource(
            ['entity' => 'calls', 'id' => '<toString(@call2->id)>', 'association' => 'activityTargets']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'users',
                        'id'         => '<toString(@user1->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@user1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact1->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact1->firstName)>'
                        ]
                    ],
                    [
                        'type'       => 'contacts',
                        'id'         => '<toString(@contact2->id)>',
                        'attributes' => [
                            'firstName' => '<toString(@contact2->firstName)>'
                        ]
                    ]
                ]
            ],
            $response,
            true
        );
        $responseContent = self::jsonToArray($response->getContent());
        foreach ($responseContent['data'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $item, sprintf('data[%s]', $key));
        }
    }

    public function testGetRelationshipForActivityTargets(): void
    {
        $response = $this->getRelationship(
            ['entity' => 'calls', 'id' => '<toString(@call2->id)>', 'association' => 'activityTargets']
        );
        $this->assertResponseContains(
            [
                'data' => [
                    ['type' => 'users', 'id' => '<toString(@user1->id)>'],
                    ['type' => 'contacts', 'id' => '<toString(@contact1->id)>'],
                    ['type' => 'contacts', 'id' => '<toString(@contact2->id)>']
                ]
            ],
            $response,
            true
        );
        $responseContent = self::jsonToArray($response->getContent());
        foreach ($responseContent['data'] as $key => $item) {
            self::assertArrayNotHasKey('meta', $item, sprintf('data[%s]', $key));
            self::assertArrayNotHasKey('attributes', $item, sprintf('data[%s]', $key));
            self::assertArrayNotHasKey('relationships', $item, sprintf('data[%s]', $key));
        }
    }

    public function testUpdateRelationshipForActivityTargets(): void
    {
        $callId = $this->getReference('call1')->getId();
        $contact2Id = $this->getReference('contact2')->getId();
        $this->patchRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'activityTargets'],
            [
                'data' => [
                    ['type' => 'contacts', 'id' => (string)$contact2Id]
                ]
            ]
        );
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertEquals([$contact2Id], $this->getActivityTargetIds($call, Contact::class));
    }

    public function testAddRelationshipForActivityTargets(): void
    {
        $callId = $this->getReference('call1')->getId();
        $contact1Id = $this->getReference('contact1')->getId();
        $contact2Id = $this->getReference('contact2')->getId();
        $this->postRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'activityTargets'],
            [
                'data' => [
                    ['type' => 'contacts', 'id' => (string)$contact1Id]
                ]
            ]
        );
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertEquals([$contact1Id, $contact2Id], $this->getActivityTargetIds($call, Contact::class));
    }

    public function testDeleteRelationshipForActivityTargets(): void
    {
        $callId = $this->getReference('call1')->getId();
        $contact1Id = $this->getReference('contact1')->getId();
        $contact2Id = $this->getReference('contact2')->getId();
        $this->deleteRelationship(
            ['entity' => 'calls', 'id' => (string)$callId, 'association' => 'activityTargets'],
            [
                'data' => [
                    ['type' => 'contacts', 'id' => (string)$contact1Id]
                ]
            ]
        );
        /** @var Call $call */
        $call = $this->getEntityManager()->find(Call::class, $callId);
        self::assertEquals([$contact2Id], $this->getActivityTargetIds($call, Contact::class));
    }

    public function testDelete(): void
    {
        $callId = $this->getReference('call1')->getId();
        $this->delete(
            ['entity' => 'calls', 'id' => (string)$callId]
        );
        self::assertTrue(null === $this->getEntityManager()->find(Call::class, $callId));
    }
}
