<?php

namespace Oro\Bundle\CallBundle\Controller;

use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Form\Handler\CallHandler;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
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
     *
     * @Route("/activity/view/{entityClass}/{entityId}", name="oro_call_activity_view")
     * @AclAncestor("oro_call_view")
     * @Template
     */
    public function activityAction($entityClass, $entityId)
    {
        return array(
            'entity' => $this->get(EntityRoutingHelper::class)->getEntity($entityClass, $entityId)
        );
    }

    /**
     * @Route("/create", name="oro_call_create")
     * @Template("@OroCall/Call/update.html.twig")
     * @Acl(
     *      id="oro_call_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="OroCallBundle:Call"
     * )
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $entity = new Call();

        $callStatus = $this->getDoctrine()
            ->getRepository('OroCallBundle:CallStatus')
            ->findOneByName('completed');
        $entity->setCallStatus($callStatus);

        $callDirection = $this->getDoctrine()
            ->getRepository('OroCallBundle:CallDirection')
            ->findOneByName('outgoing');
        $entity->setDirection($callDirection);

        $formAction = $this->get(EntityRoutingHelper::class)
            ->generateUrlByRequest('oro_call_create', $request);

        return $this->update($request, $entity, $formAction);
    }

    /**
     * @Route("/update/{id}", name="oro_call_update", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="oro_call_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="OroCallBundle:Call"
     * )
     * @param Request $request
     * @param Call $entity
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request, Call $entity)
    {
        $formAction = $this->get('router')->generate('oro_call_update', ['id' => $entity->getId()]);

        return $this->update($request, $entity, $formAction);
    }

    /**
     * @Route(name="oro_call_index")
     * @Template
     * @Acl(
     *      id="oro_call_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="OroCallBundle:Call"
     * )
     */
    public function indexAction()
    {
        return array(
            'entity_class' => Call::class
        );
    }

    /**
     * @Route("/view/{id}", name="oro_call_view")
     * @Template
     */
    public function viewAction(Call $entity)
    {
        return [
            'entity' => $entity,
        ];
    }

    /**
     * @Route("/widget", name="oro_call_widget_calls")
     * @Template
     * @AclAncestor("oro_call_view")
     *
     * @param Request $request
     * @return array
     */
    public function callsAction(Request $request)
    {
        return array(
            'datagridParameters' => $request->query->all()
        );
    }

    /**
     * @Route("/base-widget", name="oro_call_base_widget_calls")
     * @Template
     * @AclAncestor("oro_call_view")
     */
    public function baseCallsAction(Request $request)
    {
        return array(
            'datagridParameters' => $request->query->all()
        );
    }

    /**
     * @Route(
     *      "/widget/info/{id}/{renderContexts}",
     *      name="oro_call_widget_info",
     *      requirements={"id"="\d+", "renderContexts"="\d+"},
     *      defaults={"renderContexts"=true}
     * )
     * @Template("@OroCall/Call/widget/info.html.twig")
     * @AclAncestor("oro_call_view")
     */
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

        if ($this->get(CallHandler::class)->process($entity)) {
            if (!$request->get('_widgetContainer')) {
                $request->getSession()->getFlashBag()->add(
                    'success',
                    $this->get(TranslatorInterface::class)->trans('oro.call.controller.call.saved.message')
                );

                return $this->get(Router::class)->redirect($entity);
            }
            $saved = true;
        }

        return array(
            'entity'     => $entity,
            'saved'      => $saved,
            'form'       => $this->get(CallHandler::class)->getForm()->createView(),
            'formAction' => $formAction
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                EntityRoutingHelper::class,
                CallHandler::class,
                Router::class,
            ]
        );
    }
}
