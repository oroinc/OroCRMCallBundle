<?php

namespace Oro\Bundle\CallBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * REST API CRUD controller for Call entity.
 */
class CallController extends RestController
{
    /**
     * REST GET list
     *
     * @ApiDoc(
     *      description="Get all calls items",
     *      resource=true
     * )
     * @return Response
     */
    #[QueryParam(
        name: 'page',
        requirements: '\d+',
        description: 'Page number, starting from 1. Defaults to 1.',
        nullable: true
    )]
    #[QueryParam(
        name: 'limit',
        requirements: '\d+',
        description: 'Number of items per page. defaults to 10.',
        nullable: true
    )]
    #[AclAncestor('oro_call_view')]
    public function cgetAction()
    {
        return $this->handleGetListRequest();
    }

    /**
     * REST GET item
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Get call item",
     *      resource=true
     * )
     * @return Response
     */
    #[AclAncestor('oro_call_view')]
    public function getAction(int $id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT
     *
     * @param int $id call item id
     *
     * @ApiDoc(
     *      description="Update call",
     *      resource=true
     * )
     * @return Response
     */
    #[AclAncestor('oro_call_update')]
    public function putAction(int $id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new call
     *
     * @ApiDoc(
     *      description="Create new call",
     *      resource=true
     * )
     */
    #[AclAncestor('oro_call_create')]
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete call",
     *      resource=true
     * )
     * @return Response
     */
    #[Acl(id: 'oro_call_delete', type: 'entity', class: Call::class, permission: 'DELETE')]
    public function deleteAction(int $id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->container->get('oro_call.call.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->container->get('oro_call.call.form.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->container->get('oro_call.call.form.handler.api');
    }
}
