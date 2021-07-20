<?php

namespace Oro\Bundle\CallBundle\Form\Type;

use Oro\Bundle\AddressBundle\Provider\PhoneProviderInterface;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\FormBundle\Form\Type\OroDurationType;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Oro\Bundle\TranslationBundle\Form\Type\TranslatableEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallType extends AbstractType
{
    /** @var PhoneProviderInterface */
    protected $phoneProvider;

    public function __construct(PhoneProviderInterface $phoneProvider)
    {
        $this->phoneProvider = $phoneProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subject',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'oro.call.subject.label'
                ]
            )
            ->add(
                'phoneNumber',
                CallPhoneType::class,
                [
                    'required'    => true,
                    'label'       => 'oro.call.phone_number.label',
                    'suggestions' => $options['phone_suggestions']
                ]
            )
            ->add(
                'notes',
                OroResizeableRichTextType::class,
                [
                    'required' => false,
                    'label'    => 'oro.call.notes.label'
                ]
            )
            ->add(
                'callDateTime',
                OroDateTimeType::class,
                [
                    'required' => true,
                    'label'    => 'oro.call.call_date_time.label'
                ]
            )
            ->add(
                'callStatus',
                TranslatableEntityType::class,
                [
                    'required' => true,
                    'label'    => 'oro.call.call_status.label',
                    'choice_label' => 'label',
                    'class'    => 'Oro\Bundle\CallBundle\Entity\CallStatus'
                ]
            )
            ->add(
                'duration',
                OroDurationType::class,
                [
                    'required' => false,
                    'label'    => 'oro.call.duration.label'
                ]
            )
            ->add(
                'direction',
                TranslatableEntityType::class,
                [
                    'required' => true,
                    'label'    => 'oro.call.direction.label',
                    'choice_label' => 'label',
                    'class'    => 'Oro\Bundle\CallBundle\Entity\CallDirection'
                ]
            );

        if ($builder->has('contexts')) {
            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                [$this, 'addPhoneContextListener']
            );
        }
    }

    /**
     * Adds phone number owner to default contexts
     */
    public function addPhoneContextListener(FormEvent $event)
    {
        /** @var Call $entity */
        $entity = $event->getData();
        $form   = $event->getForm();

        if (!is_object($entity) || $entity->getId()) {
            return;
        }

        $contexts = $form->get('contexts')->getData();
        $phoneContexts = [];

        foreach ($contexts as $targetEntity) {
            $phones = $this->phoneProvider->getPhoneNumbers($targetEntity);
            foreach ($phones as $phone) {
                if ($entity->getPhoneNumber() === $phone[0]) {
                    $phoneContexts[] = $phone[1];
                }
            }
        }

        $form->get('contexts')->setData(array_merge($contexts, $phoneContexts));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Oro\Bundle\CallBundle\Entity\Call',
                'phone_suggestions' => []
            ]
        );
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
        return 'oro_call_form';
    }
}
