<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\AddressBundle\Provider\PhoneProviderInterface;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\Manager\CallActivityManager;
use Oro\Bundle\CallBundle\Form\Handler\CallHandler;
use Oro\Bundle\CallBundle\Tests\Unit\Fixtures\Entity\TestTarget;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Component\Testing\ReflectionUtil;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CallHandlerTest extends \PHPUnit\Framework\TestCase
{
    private const FORM_DATA = ['field' => 'value'];

    /** @var FormInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $form;

    /** @var Request */
    private $request;

    /** @var ObjectManager|\PHPUnit\Framework\MockObject\MockObject */
    private $manager;

    /** @var PhoneProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $phoneProvider;

    /** @var ActivityManager|\PHPUnit\Framework\MockObject\MockObject */
    private $activityManager;

    /** @var CallActivityManager|\PHPUnit\Framework\MockObject\MockObject */
    private $callActivityManager;

    /** @var EntityRoutingHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $entityRoutingHelper;

    /** @var FormFactory|\PHPUnit\Framework\MockObject\MockObject */
    private $formFactory;

    /** @var Call */
    private $entity;

    /** @var CallHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->form = $this->createMock(Form::class);
        $this->request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($this->request);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->phoneProvider = $this->createMock(PhoneProviderInterface::class);
        $this->activityManager = $this->createMock(ActivityManager::class);
        $this->callActivityManager = $this->createMock(CallActivityManager::class);
        $this->entityRoutingHelper = $this->createMock(EntityRoutingHelper::class);
        $this->formFactory = $this->createMock(FormFactory::class);
        $this->entity = new Call();

        $this->handler = new CallHandler(
            'oro_call_form',
            'oro_call_form',
            $requestStack,
            $this->manager,
            $this->phoneProvider,
            $this->activityManager,
            $this->callActivityManager,
            $this->entityRoutingHelper,
            $this->formFactory
        );
    }

    public function testProcessWithContexts()
    {
        $context = new User();
        ReflectionUtil::setId($context, 123);

        $owner = new User();
        ReflectionUtil::setId($owner, 321);
        $this->entity->setOwner($owner);

        $this->request->initialize([], self::FORM_DATA);
        $this->request->setMethod('POST');

        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with('oro_call_form', 'oro_call_form', $this->entity, [])
            ->willReturn($this->form);

        $this->form->expects($this->any())
            ->method('get')
            ->willReturn($this->form);
        $this->form->expects($this->any())
            ->method('has')
            ->willReturn(true);
        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        $this->form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($this->entity));
        $this->form->expects($this->any())
            ->method('getData')
            ->willReturn([$context]);

        $this->activityManager->expects($this->never())
            ->method('removeActivityTarget');
        $this->activityManager->expects($this->once())
            ->method('setActivityTargets')
            ->with(
                $this->identicalTo($this->entity),
                $this->identicalTo([$context, $owner])
            );

        $this->assertTrue(
            $this->handler->process($this->entity)
        );
    }

    public function testProcessGetRequestWithoutTargetEntity()
    {
        $this->phoneProvider->expects($this->never())
            ->method('getPhoneNumber');
        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntity');

        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with('oro_call_form', 'oro_call_form', $this->entity, [])
            ->willReturn($this->form);

        $this->form->expects($this->never())
            ->method('submit');

        $this->assertFalse($this->handler->process($this->entity));
    }

    public function testProcessGetRequestWithTargetEntity()
    {
        ReflectionUtil::setId($this->entity, 123);
        $targetEntity = new TestTarget(123);
        $targetEntity1 = new TestTarget(456);

        $this->request->query->set('entityClass', get_class($targetEntity));
        $this->request->query->set('entityId', $targetEntity->getId());

        $this->phoneProvider->expects($this->never())
            ->method('getPhoneNumber');
        $this->phoneProvider->expects($this->once())
            ->method('getPhoneNumbers')
            ->with($this->identicalTo($targetEntity))
            ->willReturn([
                ['phone1', $targetEntity],
                ['phone2', $targetEntity],
                ['phone1', $targetEntity1]
            ]);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntity')
            ->with(get_class($targetEntity), $targetEntity->getId())
            ->willReturn($targetEntity);

        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with(
                'oro_call_form',
                'oro_call_form',
                $this->entity,
                [
                    'phone_suggestions' => ['phone1', 'phone2']
                ]
            )
            ->willReturn($this->form);

        $this->form->expects($this->never())
            ->method('submit');

        $this->assertFalse($this->handler->process($this->entity));
    }

    public function testProcessGetRequestWithNewEntity()
    {
        $targetEntity = new TestTarget(123);
        $targetEntity1 = new TestTarget(456);

        $this->request->query->set('entityClass', get_class($targetEntity));
        $this->request->query->set('entityId', $targetEntity->getId());

        $this->phoneProvider->expects($this->once())
            ->method('getPhoneNumber')
            ->with($this->identicalTo($targetEntity))
            ->willReturn('phone2');
        $this->phoneProvider->expects($this->once())
            ->method('getPhoneNumbers')
            ->with($this->identicalTo($targetEntity))
            ->willReturn([
                ['phone1', $targetEntity],
                ['phone2', $targetEntity],
                ['phone1', $targetEntity1]
            ]);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntity')
            ->with(get_class($targetEntity), $targetEntity->getId())
            ->willReturn($targetEntity);

        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with(
                'oro_call_form',
                'oro_call_form',
                $this->entity,
                [
                    'phone_suggestions' => ['phone1', 'phone2']
                ]
            )
            ->willReturn($this->form);

        $this->form->expects($this->never())
            ->method('submit');

        $this->assertFalse($this->handler->process($this->entity));
        $this->assertEquals('phone2', $this->entity->getPhoneNumber());
    }

    /**
     * @dataProvider supportedMethods
     */
    public function testProcessInvalidData(string $method)
    {
        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with('oro_call_form', 'oro_call_form', $this->entity, [])
            ->willReturn($this->form);

        $this->form->expects($this->once())
            ->method('submit')
            ->with(self::FORM_DATA);
        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->phoneProvider->expects($this->never())
            ->method('getPhoneNumber');
        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntity');
        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityReference');
        $this->callActivityManager->expects($this->never())
            ->method('addAssociation');

        $this->request->initialize([], self::FORM_DATA);
        $this->request->setMethod($method);

        $this->assertFalse($this->handler->process($this->entity));
    }

    /**
     * @dataProvider supportedMethods
     */
    public function testProcessValidDataWithoutTargetEntity(string $method)
    {
        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with('oro_call_form', 'oro_call_form', $this->entity, [])
            ->willReturn($this->form);

        $this->form->expects($this->once())
            ->method('submit')
            ->with(self::FORM_DATA);
        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->phoneProvider->expects($this->never())
            ->method('getPhoneNumber');
        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntity');
        $this->entityRoutingHelper->expects($this->never())
            ->method('getEntityReference');
        $this->callActivityManager->expects($this->never())
            ->method('addAssociation');

        $this->manager->expects($this->once())
            ->method('persist')
            ->with($this->entity);
        $this->manager->expects($this->once())
            ->method('flush');

        $this->request->initialize([], self::FORM_DATA);
        $this->request->setMethod($method);

        $this->assertTrue($this->handler->process($this->entity));
    }

    /**
     * @dataProvider supportedMethods
     */
    public function testProcessValidDataWithTargetEntity(string $method)
    {
        $this->entity->setPhoneNumber('phone1');

        $targetEntity = new TestTarget(123);
        $targetEntity1 = new TestTarget(456);

        $this->formFactory->expects($this->once())
            ->method('createNamed')
            ->with('oro_call_form', 'oro_call_form', $this->entity, [])
            ->willReturn($this->form);

        $this->form->expects($this->once())
            ->method('submit')
            ->with(self::FORM_DATA);
        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->phoneProvider->expects($this->never())
            ->method('getPhoneNumber');
        $this->phoneProvider->expects($this->once())
            ->method('getPhoneNumbers')
            ->with($this->identicalTo($targetEntity))
            ->willReturn([
                ['phone1', $targetEntity],
                ['phone2', $targetEntity],
                ['phone1', $targetEntity1]
            ]);

        $this->entityRoutingHelper->expects($this->once())
            ->method('getEntity')
            ->with(get_class($targetEntity), $targetEntity->getId())
            ->willReturn($targetEntity);
        $this->callActivityManager->expects($this->exactly(3))
            ->method('addAssociation')
            ->withConsecutive(
                [$this->identicalTo($this->entity), $this->identicalTo($targetEntity)], // phone1, $targetEntity
                [$this->identicalTo($this->entity), $this->identicalTo($targetEntity)], // phone2, $targetEntity
                [$this->identicalTo($this->entity), $this->identicalTo($targetEntity1)] // phone1, $targetEntity1
            );

        $this->manager->expects($this->once())
            ->method('persist')
            ->with($this->entity);
        $this->manager->expects($this->once())
            ->method('flush');

        $this->request->initialize(
            [
                'entityClass' => get_class($targetEntity),
                'entityId' => $targetEntity->getId()
            ],
            self::FORM_DATA
        );
        $this->request->setMethod($method);

        $this->assertTrue($this->handler->process($this->entity));
    }

    public function supportedMethods(): array
    {
        return [
            ['POST'],
            ['PUT']
        ];
    }
}
