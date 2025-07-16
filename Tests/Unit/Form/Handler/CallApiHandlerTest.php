<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Form\Handler\CallApiHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CallApiHandlerTest extends TestCase
{
    private const FORM_DATA = ['field' => 'value'];

    private FormInterface&MockObject $form;
    private Request $request;
    private ObjectManager&MockObject $manager;
    private Call $entity;
    private CallApiHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        $this->form = $this->createMock(Form::class);
        $this->request = new Request();
        $this->manager = $this->createMock(ObjectManager::class);
        $this->entity = new Call();

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->handler = new CallApiHandler($this->form, $requestStack, $this->manager);
    }

    public function testProcessUnsupportedRequest(): void
    {
        $this->form->expects(self::once())
            ->method('setData')
            ->with($this->entity);

        $this->form->expects(self::never())
            ->method('submit');

        self::assertFalse($this->handler->process($this->entity));
    }

    /**
     * @dataProvider supportedMethods
     */
    public function testProcessSupportedRequest(string $method): void
    {
        $this->form->expects(self::once())
            ->method('setData')
            ->with($this->entity);

        $this->request->initialize([], self::FORM_DATA);
        $this->request->setMethod($method);

        $this->form->expects(self::once())
            ->method('submit')
            ->with(self::FORM_DATA);

        self::assertFalse($this->handler->process($this->entity));
    }

    public function supportedMethods(): array
    {
        return [
            ['POST'],
            ['PUT'],
        ];
    }

    public function testProcessValidData(): void
    {
        $this->request->initialize([], self::FORM_DATA);
        $this->request->setMethod('POST');

        $this->form->expects(self::once())
            ->method('setData')
            ->with($this->entity);

        $this->form->expects(self::once())
            ->method('submit')
            ->with(self::FORM_DATA);

        $this->form->expects(self::once())
            ->method('isValid')
            ->willReturn(true);

        $this->manager->expects(self::once())
            ->method('persist')
            ->with($this->entity);

        $this->manager->expects(self::once())
            ->method('flush');

        self::assertTrue($this->handler->process($this->entity));
    }
}
