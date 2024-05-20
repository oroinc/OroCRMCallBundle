<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;

class CallStatusTest extends RestJsonApiTestCase
{
    public function testGetList(): void
    {
        $response = $this->cget(['entity' => 'callstatuses']);
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'callstatuses',
                        'id'         => 'completed',
                        'attributes' => [
                            'label' => 'Completed'
                        ]
                    ],
                    [
                        'type'       => 'callstatuses',
                        'id'         => 'in_progress',
                        'attributes' => [
                            'label' => 'In progress'
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGet(): void
    {
        $response = $this->get(['entity' => 'callstatuses', 'id' => 'completed']);
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'callstatuses',
                    'id'         => 'completed',
                    'attributes' => [
                        'label' => 'Completed'
                    ]
                ]
            ],
            $response
        );
    }

    public function testTryToCreate(): void
    {
        $response = $this->post(
            ['entity' => 'callstatuses', 'id' => 'new_status'],
            ['data' => ['type' => 'callstatuses', 'id' => 'new_status']],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDelete(): void
    {
        $response = $this->delete(
            ['entity' => 'callstatuses', 'id' => 'completed'],
            [],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDeleteList(): void
    {
        $response = $this->cdelete(
            ['entity' => 'callstatuses'],
            ['filter[id]' => 'completed'],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testGetOptionsForList(): void
    {
        $response = $this->options(
            $this->getListRouteName(),
            ['entity' => 'callstatuses']
        );
        self::assertAllowResponseHeader($response, 'OPTIONS, GET');
    }

    public function testOptionsForItem(): void
    {
        $response = $this->options(
            $this->getItemRouteName(),
            ['entity' => 'callstatuses', 'id' => 'completed']
        );
        self::assertAllowResponseHeader($response, 'OPTIONS, GET');
    }
}
