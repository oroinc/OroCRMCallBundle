<?php

namespace Oro\Bundle\CallBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\EntityExtendBundle\Tools\AssociationNameGenerator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CallApiHandler
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param RequestStack  $requestStack
     * @param ObjectManager $manager
     */
    public function __construct(FormInterface $form, RequestStack $requestStack, ObjectManager $manager)
    {
        $this->form = $form;
        $this->requestStack = $requestStack;
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param  Call $entity
     * @return bool  True on successful processing, false otherwise
     */
    public function process(Call $entity)
    {
        $this->form->setData($entity);

        $request = $this->requestStack->getCurrentRequest();
        if (in_array($request->getMethod(), ['POST', 'PUT'], true)) {
            $data = $this->form->getName()
                ? $request->request->get($this->form->getName())
                : $request->request->all();
            $this->form->submit($data);

            if ($this->form->isValid()) {
                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param Call $entity
     */
    protected function onSuccess(Call $entity)
    {
        $this->handleAssociations($entity);
        $this->manager->persist($entity);
        $this->manager->flush();
    }

    /**
     * Add associations to call item
     *
     * @param Call $entity
     */
    protected function handleAssociations(Call $entity)
    {
        $associationsFormField = $this->form->get('associations');
        if (!$associationsFormField) {
            return;
        }
        $associations = $associationsFormField->getData();
        if (empty($associations)) {
            return;
        }
        foreach ($associations as $association) {
            $associationType = isset($association['type']) ? $association['type'] : null;
            $target          = $this->manager->getReference($association['entityName'], $association['entityId']);
            call_user_func(
                [
                    $entity,
                    AssociationNameGenerator::generateAddTargetMethodName($associationType)
                ],
                $target
            );
        }
    }
}
