<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Controller\API\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;

class CallControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient([], $this->generateWsseAuthHeader());
    }

    /**
     * @return array
     */
    public function testCreate()
    {
        $request = [
            'call' => [
                'subject'      => 'Test Call ' . mt_rand(),
                'owner'        => '1',
                'duration'     => '00:00:05',
                'direction'    => 'outgoing',
                'callDateTime' => date('c'),
                'phoneNumber'  => '123-123=123',
                'callStatus'   => 'completed',
                'associations' => [
                    [
                        'entityName' => User::class,
                        'entityId'   => 1,
                        'type'       => 'activity'
                    ],
                ]
            ]
        ];
        $this->client->jsonRequest(
            'POST',
            $this->getUrl('oro_api_post_call'),
            $request
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 201);

        $this->assertArrayHasKey('id', $result);

        $request['id'] = $result['id'];
        return $request;
    }

    /**
     * @depends testCreate
     */
    public function testGet(array $request)
    {
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_calls')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $id = $request['id'];
        $result = array_filter(
            $result,
            function ($a) use ($id) {
                return $a['id'] == $id;
            }
        );

        $this->assertNotEmpty($result);
        $this->assertEquals($request['call']['subject'], reset($result)['subject']);

        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_call', ['id' => $id])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($request['call']['subject'], $result['subject']);
        $this->assertEquals(5, $result['duration']);
    }

    /**
     * @depends testCreate
     * @depends testGet
     */
    public function testUpdate(array $request)
    {
        $request['call']['subject'] .= '_Updated';
        $this->client->jsonRequest(
            'PUT',
            $this->getUrl('oro_api_put_call', ['id' => $request['id']]),
            $request
        );
        $result = $this->client->getResponse();

        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_call', ['id' => $request['id']])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals(
            $request['call']['subject'],
            $result['subject']
        );
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $request)
    {
        $this->client->jsonRequest(
            'DELETE',
            $this->getUrl('oro_api_delete_call', ['id' => $request['id']])
        );
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_call', ['id' => $request['id']])
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }

    /**
     * @return array
     */
    public function testCreateWithSecondsDuration()
    {
        $request = [
            'call' => [
                'subject'      => 'Test Call ' . mt_rand(),
                'owner'        => '1',
                'duration'     => '23.5h 13.5s',
                'direction'    => 'outgoing',
                'callDateTime' => date('c'),
                'phoneNumber'  => '123-123=123',
                'callStatus'   => 'completed',
                'associations' => [
                    [
                        'entityName' => User::class,
                        'entityId'   => 1,
                        'type'       => 'activity'
                    ],
                ]
            ]
        ];
        $this->client->jsonRequest(
            'POST',
            $this->getUrl('oro_api_post_call'),
            $request
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 201);

        $this->assertArrayHasKey('id', $result);

        $request['id'] = $result['id'];

        return $request;
    }

    /**
     * @depends testCreateWithSecondsDuration
     */
    public function testGetWithSeconds(array $request)
    {
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_call', ['id' => $request['id']])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $duration = (23 * 60 * 60) + (30 * 60) + 14; //23.5h 13.5s
        $this->assertEquals($duration, $result['duration']);
    }

    public function testCreateWithoutCallDateTime()
    {
        $request = [
            'call' => [
                'subject'      => 'Test Call ' . mt_rand(),
                'owner'        => '1',
                'duration'     => '00:00:05',
                'direction'    => 'outgoing',
                'callDateTime' => null,
                'phoneNumber'  => '123-123=123',
                'callStatus'   => 'completed',
                'associations' => [
                    [
                        'entityName' => User::class,
                        'entityId'   => 1,
                        'type'       => 'activity'
                    ],
                ]
            ]
        ];
        $this->client->jsonRequest(
            'POST',
            $this->getUrl('oro_api_post_call'),
            $request
        );

        $this->assertJsonResponseStatusCodeEquals($this->client->getResponse(), 400);
    }
}
