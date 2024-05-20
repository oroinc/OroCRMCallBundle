<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Api\RestJsonApi;

use Oro\Bundle\ApiBundle\Tests\Functional\RestJsonApiTestCase;

class CallDirectionTest extends RestJsonApiTestCase
{
    public function testGetList(): void
    {
        $response = $this->cget(['entity' => 'calldirections']);
        $this->assertResponseContains(
            [
                'data' => [
                    [
                        'type'       => 'calldirections',
                        'id'         => 'incoming',
                        'attributes' => [
                            'label' => 'Incoming'
                        ]
                    ],
                    [
                        'type'       => 'calldirections',
                        'id'         => 'outgoing',
                        'attributes' => [
                            'label' => 'Outgoing'
                        ]
                    ]
                ]
            ],
            $response
        );
    }

    public function testGet(): void
    {
        $response = $this->get(['entity' => 'calldirections', 'id' => 'incoming']);
        $this->assertResponseContains(
            [
                'data' => [
                    'type'       => 'calldirections',
                    'id'         => 'incoming',
                    'attributes' => [
                        'label' => 'Incoming'
                    ]
                ]
            ],
            $response
        );
    }

    public function testTryToCreate(): void
    {
        $response = $this->post(
            ['entity' => 'calldirections', 'id' => 'new_status'],
            ['data' => ['type' => 'calldirections', 'id' => 'new_status']],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDelete(): void
    {
        $response = $this->delete(
            ['entity' => 'calldirections', 'id' => 'incoming'],
            [],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testTryToDeleteList(): void
    {
        $response = $this->cdelete(
            ['entity' => 'calldirections'],
            ['filter[id]' => 'incoming'],
            [],
            false
        );
        self::assertMethodNotAllowedResponse($response, 'OPTIONS, GET');
    }

    public function testGetOptionsForList(): void
    {
        $response = $this->options(
            $this->getListRouteName(),
            ['entity' => 'calldirections']
        );
        self::assertAllowResponseHeader($response, 'OPTIONS, GET');
    }

    public function testOptionsForItem(): void
    {
        $response = $this->options(
            $this->getItemRouteName(),
            ['entity' => 'calldirections', 'id' => 'incoming']
        );
        self::assertAllowResponseHeader($response, 'OPTIONS, GET');
    }
}
