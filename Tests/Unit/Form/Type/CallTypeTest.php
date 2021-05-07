<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Form\Type;

use Oro\Bundle\AddressBundle\Provider\PhoneProvider;
use Oro\Bundle\CallBundle\Form\Type\CallPhoneType;
use Oro\Bundle\CallBundle\Form\Type\CallType;
use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\FormBundle\Form\Type\OroDurationType;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Oro\Bundle\TranslationBundle\Form\Type\TranslatableEntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallTypeTest extends FormIntegrationTestCase
{
    /** @var CallType */
    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $phoneProvider = $this->createMock(PhoneProvider::class);

        $this->type = new CallType($phoneProvider);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->configureOptions($resolver);
    }

    public function testBuildForm()
    {
        $formFields = [
            ['subject', TextType::class],
            ['phoneNumber', CallPhoneType::class],
            ['notes', OroResizeableRichTextType::class],
            ['callDateTime', OroDateTimeType::class],
            ['callStatus', TranslatableEntityType::class],
            ['duration', OroDurationType::class],
            ['direction', TranslatableEntityType::class]
        ];

        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->exactly(count($formFields)))
            ->method('add')
            ->withConsecutive(...$formFields)
            ->willReturnSelf();

        $options = [
            'phone_suggestions' => []
        ];
        $this->type->buildForm($builder, $options);
    }
}
