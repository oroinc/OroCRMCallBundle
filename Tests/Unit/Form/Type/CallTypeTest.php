<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Form\Type;

use Oro\Bundle\CallBundle\Form\Type\CallPhoneType;
use Oro\Bundle\CallBundle\Form\Type\CallType;
use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\FormBundle\Form\Type\OroDurationType;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Oro\Bundle\TranslationBundle\Form\Type\TranslatableEntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class CallTypeTest extends FormIntegrationTestCase
{
    /**
     * @var CallType
     */
    protected $type;

    protected function setUp(): void
    {
        parent::setUp();

        $phoneProvider = $this->getMockBuilder('Oro\Bundle\AddressBundle\Provider\PhoneProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->type = new CallType($phoneProvider);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock('Symfony\Component\OptionsResolver\OptionsResolver');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->configureOptions($resolver);
    }

    public function testBuildForm()
    {
        $expectedFields = [
            'subject' => TextType::class,
            'phoneNumber' => CallPhoneType::class,
            'notes' => OroResizeableRichTextType::class,
            'callDateTime' => OroDateTimeType::class,
            'callStatus' => TranslatableEntityType::class,
            'duration' => OroDurationType::class,
            'direction' => TranslatableEntityType::class
        ];

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $counter = 0;
        foreach ($expectedFields as $fieldName => $formType) {
            $builder->expects($this->at($counter))
                ->method('add')
                ->with($fieldName, $formType)
                ->will($this->returnSelf());
            $counter++;
        }
        $options = [
            'phone_suggestions' => []
        ];
        $this->type->buildForm($builder, $options);
    }
}
