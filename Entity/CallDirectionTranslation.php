<?php

namespace Oro\Bundle\CallBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Oro\Bundle\LocaleBundle\Entity\AbstractTranslation;

/**
 * Represents Gedmo translation dictionary for CallDirection entity.
 */
#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\Table(name: 'orocrm_call_direction_trans')]
#[ORM\Index(columns: ['locale', 'object_class', 'field', 'foreign_key'], name: 'oro_call_direction_trans_idx')]
class CallDirectionTranslation extends AbstractTranslation
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'foreign_key', type: Types::STRING, length: 32)]
    protected $foreignKey;
}
