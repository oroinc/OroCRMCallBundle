<?php

namespace Oro\Bundle\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\LocaleBundle\Entity\AbstractTranslation;

/**
 * Represents Gedmo translation dictionary for CallDirection entity.
 *
 * @ORM\Table(name="orocrm_call_direction_trans", indexes={
 *      @ORM\Index(
 *          name="oro_call_direction_trans_idx", columns={"locale", "object_class", "field", "foreign_key"}
 *      )
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class CallDirectionTranslation extends AbstractTranslation
{
    /**
     * @var string $foreignKey
     *
     * @ORM\Column(name="foreign_key", type="string", length=32)
     */
    protected $foreignKey;
}
