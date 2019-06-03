<?php

namespace Oro\Bundle\CallBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroUnstructuredHiddenType;
use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * The form type for old REST API of Call entity.
 */
class CallApiType extends AbstractType
{
    const NAME = 'oro_call_form_api';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add a hidden field to pass form validation
        $builder->add(
            'associations',
            OroUnstructuredHiddenType::class,
            [
                'mapped'      => false,
                'constraints' => [
                    new Callback(['callback' => [$this, 'validateAssociations']])
                ]
            ]
        );

        $builder->addEventSubscriber(new PatchSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'csrf_protection' => false,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CallType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * @param array                     $associations
     * @param ExecutionContextInterface $context
     */
    public function validateAssociations($associations, ExecutionContextInterface $context)
    {
        if (empty($associations)) {
            return;
        }

        foreach ($associations as $index => $association) {
            if (empty($association['entityName']) || empty($association['entityId'])) {
                $context->addViolation(
                    'Invalid association provided at position {{index}}. Entity Name and Entity ID should not be null.',
                    [
                        '{{index}}' => $index+1
                    ]
                );
            }
        }
    }
}
