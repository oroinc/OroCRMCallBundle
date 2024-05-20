<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiUpdateListTestCase;
use Oro\Bundle\CallBundle\Entity\Call;

/**
 * @dbIsolationPerTest
 */
class CallUpdateListTest extends RestJsonApiUpdateListTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures(['@OroCallBundle/Tests/Functional/Api/DataFixtures/call_data.yml']);
    }

    public function testCreateEntities(): void
    {
        $this->processUpdateList(
            Call::class,
            [
                'data' => [
                    [
                        'type'          => 'calls',
                        'attributes'    => [
                            'subject'     => 'New Call 1',
                            'phoneNumber' => '123-123'
                        ],
                        'relationships' => [
                            'direction'  => [
                                'data' => [
                                    'type' => 'calldirections',
                                    'id'   => '<toString(@call_direction_incoming->name)>'
                                ]
                            ],
                            'callStatus' => [
                                'data' => [
                                    'type' => 'callstatuses',
                                    'id'   => '<toString(@call_status_in_progress->name)>'
                                ]
                            ]
                        ]
                    ],
                    [
                        'type'          => 'calls',
                        'attributes'    => [
                            'subject'     => 'New Call 2',
                            'phoneNumber' => '123-123'
                        ],
                        'relationships' => [
                            'direction'  => [
                                'data' => [
                                    'type' => 'calldirections',
                                    'id'   => '<toString(@call_direction_incoming->name)>'
                                ]
                            ],
                            'callStatus' => [
                                'data' => [
                                    'type' => 'callstatuses',
                                    'id'   => '<toString(@call_status_in_progress->name)>'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $response = $this->cget(['entity' => 'calls'], ['fields[calls]' => 'subject']);
        $responseContent = $this->updateResponseContent(
            [
                'data' => [
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => ['subject' => 'Create New Contact']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call2->id)>',
                        'attributes' => ['subject' => 'Call 2']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call3->id)>',
                        'attributes' => ['subject' => 'Call 3']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => 'new',
                        'attributes' => ['subject' => 'New Call 1']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => 'new',
                        'attributes' => ['subject' => 'New Call 2']
                    ]
                ]
            ],
            $response
        );
        $this->assertResponseContains($responseContent, $response);
    }

    public function testUpdateEntities(): void
    {
        $this->processUpdateList(
            Call::class,
            [
                'data' => [
                    [
                        'meta'       => ['update' => true],
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => ['subject' => 'Updated Call 1']
                    ],
                    [
                        'meta'       => ['update' => true],
                        'type'       => 'calls',
                        'id'         => '<toString(@call2->id)>',
                        'attributes' => ['subject' => 'Updated Call 2']
                    ]
                ]
            ]
        );

        $response = $this->cget(['entity' => 'calls'], ['fields[calls]' => 'subject']);
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => ['subject' => 'Updated Call 1']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call2->id)>',
                        'attributes' => ['subject' => 'Updated Call 2']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call3->id)>',
                        'attributes' => ['subject' => 'Call 3']
                    ]
                ]
            ],
            $response
        );
    }

    public function testCreateAndUpdateEntities(): void
    {
        $this->processUpdateList(
            Call::class,
            [
                'data' => [
                    [
                        'type'          => 'calls',
                        'attributes'    => [
                            'subject'     => 'New Call 1',
                            'phoneNumber' => '123-123'
                        ],
                        'relationships' => [
                            'direction'  => [
                                'data' => [
                                    'type' => 'calldirections',
                                    'id'   => '<toString(@call_direction_incoming->name)>'
                                ]
                            ],
                            'callStatus' => [
                                'data' => [
                                    'type' => 'callstatuses',
                                    'id'   => '<toString(@call_status_in_progress->name)>'
                                ]
                            ]
                        ]
                    ],
                    [
                        'meta'       => ['update' => true],
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => ['subject' => 'Updated Call 1']
                    ]
                ]
            ]
        );

        $response = $this->cget(['entity' => 'calls'], ['fields[calls]' => 'subject']);
        $responseContent = $this->updateResponseContent(
            [
                'data' => [
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call1->id)>',
                        'attributes' => ['subject' => 'Updated Call 1']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call2->id)>',
                        'attributes' => ['subject' => 'Call 2']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => '<toString(@call3->id)>',
                        'attributes' => ['subject' => 'Call 3']
                    ],
                    [
                        'type'       => 'calls',
                        'id'         => 'new',
                        'attributes' => ['subject' => 'New Call 1']
                    ]
                ]
            ],
            $response
        );
        $this->assertResponseContains($responseContent, $response);
    }
}
