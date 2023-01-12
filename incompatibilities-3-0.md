- [CallBundle](#callbundle)

CallBundle
----------
* The following methods in class `CallActivityListProvider`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Provider/CallActivityListProvider.php#L42 "Oro\Bundle\CallBundle\Provider\CallActivityListProvider")</sup> were changed:
  > - `__construct(DoctrineHelper $doctrineHelper, ServiceLink $entityOwnerAccessorLink, ActivityAssociationHelper $activityAssociationHelper, CommentAssociationHelper $commentAssociationHelper)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Provider/CallActivityListProvider.php#L42 "Oro\Bundle\CallBundle\Provider\CallActivityListProvider")</sup>
  > - `__construct(DoctrineHelper $doctrineHelper, ServiceLink $entityOwnerAccessorLink, ActivityAssociationHelper $activityAssociationHelper, CommentAssociationHelper $commentAssociationHelper)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Provider/CallActivityListProvider.php#L42 "Oro\Bundle\CallBundle\Provider\CallActivityListProvider")</sup>

  > - `getRoutes()`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Provider/CallActivityListProvider.php#L139 "Oro\Bundle\CallBundle\Provider\CallActivityListProvider")</sup>
  > - `getRoutes($activityEntity)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Provider/CallActivityListProvider.php#L139 "Oro\Bundle\CallBundle\Provider\CallActivityListProvider")</sup>

* The `CallApiHandler::__construct(FormInterface $form, Request $request, ObjectManager $manager)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Handler/CallApiHandler.php#L35 "Oro\Bundle\CallBundle\Form\Handler\CallApiHandler")</sup> method was changed to `CallApiHandler::__construct(FormInterface $form, RequestStack $requestStack, ObjectManager $manager)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Form/Handler/CallApiHandler.php#L36 "Oro\Bundle\CallBundle\Form\Handler\CallApiHandler")</sup>
* The `CallHandler::__construct($formName, $formType, Request $request, ObjectManager $manager, PhoneProviderInterface $phoneProvider, ActivityManager $activityManager, CallActivityManager $callActivityManager, EntityRoutingHelper $entityRoutingHelper, FormFactory $formFactory)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Handler/CallHandler.php#L60 "Oro\Bundle\CallBundle\Form\Handler\CallHandler")</sup> method was changed to `CallHandler::__construct($formName, $formType, RequestStack $requestStack, ObjectManager $manager, PhoneProviderInterface $phoneProvider, ActivityManager $activityManager, CallActivityManager $callActivityManager, EntityRoutingHelper $entityRoutingHelper, FormFactory $formFactory)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Form/Handler/CallHandler.php#L61 "Oro\Bundle\CallBundle\Form\Handler\CallHandler")</sup>
* The following methods in class `CallController`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Controller/CallController.php#L43 "Oro\Bundle\CallBundle\Controller\CallController")</sup> were changed:
  > - `createAction()`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Controller/CallController.php#L42 "Oro\Bundle\CallBundle\Controller\CallController")</sup>
  > - `createAction(Request $request)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Controller/CallController.php#L43 "Oro\Bundle\CallBundle\Controller\CallController")</sup>

  > - `update(Call $entity, $formAction)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Controller/CallController.php#L158 "Oro\Bundle\CallBundle\Controller\CallController")</sup>
  > - `update(Request $request, Call $entity, $formAction)`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/3.0.0/Controller/CallController.php#L163 "Oro\Bundle\CallBundle\Controller\CallController")</sup>

* The `CallApiType::setDefaultOptions`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Type/CallApiType.php#L37 "Oro\Bundle\CallBundle\Form\Type\CallApiType::setDefaultOptions")</sup> method was removed.
* The `CallPhoneType::setDefaultOptions`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Type/CallPhoneType.php#L16 "Oro\Bundle\CallBundle\Form\Type\CallPhoneType::setDefaultOptions")</sup> method was removed.
* The `CallType::setDefaultOptions`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Type/CallType.php#L134 "Oro\Bundle\CallBundle\Form\Type\CallType::setDefaultOptions")</sup> method was removed.
* The `CallApiHandler::$request`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Handler/CallApiHandler.php#L23 "Oro\Bundle\CallBundle\Form\Handler\CallApiHandler::$request")</sup> property was removed.
* The `CallHandler::$request`<sup>[[?]](https://github.com/oroinc/OroCRMCallBundle/tree/2.6.0/Form/Handler/CallHandler.php#L29 "Oro\Bundle\CallBundle\Form\Handler\CallHandler::$request")</sup> property was removed.

