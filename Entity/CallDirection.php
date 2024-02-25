<?php

namespace Oro\Bundle\CallBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;

/**
 * Entity that represents Call Direction
 */
#[ORM\Entity]
#[ORM\Table(name: 'orocrm_call_direction')]
#[Gedmo\TranslationEntity(class: CallDirectionTranslation::class)]
#[Config(
    defaultValues: [
        'grouping' => ['groups' => ['dictionary']],
        'dictionary' => ['virtual_fields' => ['label'], 'search_fields' => ['label'], 'representation_field' => 'label']
    ]
)]
class CallDirection implements Translatable
{
    #[ORM\Column(name: 'name', type: Types::STRING, length: 32)]
    #[ORM\Id]
    protected ?string $name = null;

    #[ORM\Column(name: 'label', type: Types::STRING, length: 255, unique: true)]
    #[Gedmo\Translatable]
    protected ?string $label = null;

    #[Gedmo\Locale]
    protected ?string $locale = null;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address type label
     *
     * @param string $label
     * @return CallDirection
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get address type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return CallDirection
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Returns locale code
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->label;
    }
}
