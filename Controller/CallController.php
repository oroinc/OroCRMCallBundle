<?php

namespace Oro\Bundle\CallBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\CallDirection;
use Oro\Bundle\CallBundle\Entity\CallStatus;
use Oro\Bundle\CallBundle\Form\Handler\CallHandler;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * CRUD controllers for calls.
 */
class CallController extends AbstractController
{
    /**
     * This action is used to render the list of calls associated with the given entity
     * on the view page of this entity
     */
    #[Route(path: '/activity/view/{entityClass}/{entityId}', name: 'oro_call_activity_view')]
    #[Template]
    #[AclAncestor('oro_call_view')]
    public function activityAction($entityClass, $entityId)
    {
        return array(
            'entity' => $this->container->get(EntityRoutingHelper::class)->getEntity($entityClass, $entityId)
        );
    }

    /**
     * @param Request $request
     * @return array|RedirectResponse
     */
    #[Route(path: '/create', name: 'oro_call_create')]
    #[Template('@OroCall/Call/update.html.twig')]
    #[Acl(id: 'oro_call_create', type: 'entity', class: Call::class, permission: 'CREATE')]
    public function createAction(Request $request)
    {
        $entity = new Call();

        $callStatus = $this->container->get('doctrine')
            ->getRepository(CallStatus::class)
            ->findOneByName('completed');
        $entity->setCallStatus($callStatus);

        $callDirection = $this->container->get('doctrine')
            ->getRepository(CallDirection::class)
            ->findOneByName('outgoing');
        $entity->setDirection($callDirection);

        $formAction = $this->container->get(EntityRoutingHelper::class)
            ->generateUrlByRequest('oro_call_create', $request);

        return $this->update($request, $entity, $formAction);
    }

    /**
     * @param Request $request
     * @param Call $entity
     * @return array|RedirectResponse
     */
    #[Route(path: '/update/{id}', name: 'oro_call_update', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'oro_call_update', type: 'entity', class: Call::class, permission: 'EDIT')]
    public function updateAction(Request $request, Call $entity)
    {
        $formAction = $this->container->get('router')->generate('oro_call_update', ['id' => $entity->getId()]);

        return $this->update($request, $entity, $formAction);
    }

    #[Route(name: 'oro_call_index')]
    #[Template]
    #[Acl(id: 'oro_call_view', type: 'entity', class: Call::class, permission: 'VIEW')]
    public function indexAction()
    {
        return array(
            'entity_class' => Call::class
        );
    }

    #[Route(path: '/view/{id}', name: 'oro_call_view')]
    #[Template]
    #[AclAncestor('oro_call_view')]
    public function viewAction(Call $entity)
    {
        return [
            'entity' => $entity,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    #[Route(path: '/widget', name: 'oro_call_widget_calls')]
    #[Template]
    #[AclAncestor('oro_call_view')]
    public function callsAction(Request $request)
    {
        return array(
            'datagridParameters' => $request->query->all()
        );
    }

    #[Route(path: '/base-widget', name: 'oro_call_base_widget_calls')]
    #[Template]
    #[AclAncestor('oro_call_view')]
    public function baseCallsAction(Request $request)
    {
        return array(
            'datagridParameters' => $request->query->all()
        );
    }

    #[Route(
        path: '/widget/info/{id}/{renderContexts}',
        name: 'oro_call_widget_info',
        requirements: ['id' => '\d+', 'renderContexts' => '\d+'],
        defaults: ['renderContexts' => true]
    )]
    #[Template('@OroCall/Call/widget/info.html.twig')]
    #[AclAncestor('oro_call_view')]
    public function infoAction(Call $entity, $renderContexts)
    {
        return [
            'entity'         => $entity,
            'renderContexts' => (bool)$renderContexts
        ];
    }

    /**
     * @param Request $request
     * @param Call $entity
     * @param string $formAction
     *
     * @return array
     */
    protected function update(Request $request, Call $entity, $formAction)
    {
        $saved = false;

        if ($this->container->get(CallHandler::class)->process($entity)) {
            if (!$request->get('_widgetContainer')) {
                $request->getSession()->getFlashBag()->add(
                    'success',
                    $this->container->get(TranslatorInterface::class)->trans('oro.call.controller.call.saved.message')
                );

                return $this->container->get(Router::class)->redirect($entity);
            }
            $saved = true;
        }

        return array(
            'entity'     => $entity,
            'saved'      => $saved,
            'form'       => $this->container->get(CallHandler::class)->getForm()->createView(),
            'formAction' => $formAction
        );
    }

    #[\Override]
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                EntityRoutingHelper::class,
                CallHandler::class,
                Router::class,
                'doctrine' => ManagerRegistry::class,
            ]
        );
    }
}
