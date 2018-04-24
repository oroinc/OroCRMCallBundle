<?php

namespace Oro\Bundle\CallBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\Select2HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallPhoneType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $defaultConfigs = [
            'allowClear'   => true,
            'placeholder'  => 'oro.call.form.choose_or_enter_phone',
            'component'    => 'call-phone'
        ];

        $resolver->setDefaults(
            [
                'suggestions' => [],
                'random_id'   => true,
                'configs'     => $defaultConfigs,
            ]
        );

        $resolver->setNormalizer(
            'configs',
            function (Options $options, $configs) use (&$defaultConfigs) {
                return array_merge($defaultConfigs, $configs);
            }
        )
        ->setNormalizer(
            'suggestions',
            function (Options $options, $suggestions) {
                sort($suggestions, SORT_STRING & SORT_FLAG_CASE);

                return $suggestions;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['suggestions'] = $options['suggestions'];
        $view->vars['component_options']['suggestions'] = $options['suggestions'];
        $view->vars['component_options']['value'] = $view->vars['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Select2HiddenType::class;
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
        return 'oro_call_phone';
    }
}
