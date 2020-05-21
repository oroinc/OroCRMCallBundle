<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Form;

class CallControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient(
            array(),
            array_merge($this->generateBasicAuthHeader())
        );
        $this->client->useHashNavigation(true);
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('oro_call_index'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('oro_call_create'));
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_call_form[subject]'] = 'Test Call';
        $form['oro_call_form[duration]'] = '00:00:05';
        $form['oro_call_form[notes]'] = 'Call Notes';
        $form['oro_call_form[phoneNumber]'] = '123-123-123';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        static::assertStringContainsString("Call saved", $crawler->html());

        $call = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository('OroCallBundle:Call')
            ->findOneBySubject('Test Call');
        $this->assertNotNull($call);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'calls-grid',
            array('calls-grid[_filter][subject][value]' => 'Test Call')
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $id = $result['id'];
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_call_update', array('id' => $result['id']))
        );
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_call_form[subject]'] = 'Test Update Call';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        static::assertStringContainsString("Call saved", $crawler->html());

        return $id;
    }

    /**
     * @depends testUpdate
     */
    public function testView($id)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_call_view', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        static::assertStringContainsString("Test Update Call", $crawler->html());
    }

    /**
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $this->ajaxRequest(
            'DELETE',
            $this->getUrl('oro_api_delete_call', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('oro_call_update', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);
    }
}
