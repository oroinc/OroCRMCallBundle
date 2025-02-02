<?php

namespace Oro\Bundle\CallBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Extend\Entity\Autocomplete\OroCallBundle_Entity_Call;
use Oro\Bundle\ActivityBundle\Model\ActivityInterface;
use Oro\Bundle\ActivityBundle\Model\ExtendActivity;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Entity that represents Call
 *
 * @mixin OroCallBundle_Entity_Call
 */
#[ORM\Entity]
#[ORM\Table(name: 'orocrm_call')]
#[ORM\Index(columns: ['call_date_time', 'id'], name: 'call_dt_idx')]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'oro_call_index',
    defaultValues: [
        'entity' => ['icon' => 'fa-phone-square'],
        'ownership' => [
            'owner_type' => 'USER',
            'owner_field_name' => 'owner',
            'owner_column_name' => 'owner_id',
            'organization_field_name' => 'organization',
            'organization_column_name' => 'organization_id'
        ],
        'security' => ['type' => 'ACL', 'group_name' => '', 'category' => 'account_management'],
        'grouping' => ['groups' => ['activity']],
        'activity' => [
            'route' => 'oro_call_activity_view',
            'acl' => 'oro_call_view',
            'action_button_widget' => 'oro_log_call_button',
            'action_link_widget' => 'oro_log_call_link'
        ],
        'grid' => ['default' => 'calls-grid', 'context' => 'call-for-context-grid']
    ]
)]
class Call implements DatesAwareInterface, ActivityInterface, ExtendEntityInterface
{
    use ExtendActivity;
    use ExtendEntityTrait;

    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?User $owner = null;

    #[ORM\Column(name: 'subject', type: Types::STRING, length: 255)]
    protected ?string $subject = null;

    #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $phoneNumber = null;

    #[ORM\Column(name: 'notes', type: Types::TEXT, nullable: true)]
    protected ?string $notes = null;

    #[ORM\Column(name: 'call_date_time', type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $callDateTime = null;

    #[ORM\ManyToOne(targetEntity: CallStatus::class)]
    #[ORM\JoinColumn(name: 'call_status_name', referencedColumnName: 'name', onDelete: 'SET NULL')]
    protected ?CallStatus $callStatus = null;

    /**
     * @var int
     */
    #[ORM\Column(name: 'duration', type: 'duration', nullable: true)]
    protected $duration;

    #[ORM\ManyToOne(targetEntity: CallDirection::class)]
    #[ORM\JoinColumn(name: 'call_direction_name', referencedColumnName: 'name', onDelete: 'SET NULL')]
    protected ?CallDirection $direction = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    #[ConfigField(defaultValues: ['entity' => ['label' => 'oro.ui.created_at']])]
    protected ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    #[ConfigField(defaultValues: ['entity' => ['label' => 'oro.ui.updated_at']])]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected ?OrganizationInterface $organization = null;

    /**
     * @var bool
     */
    protected $updatedAtSet;

    public function __construct()
    {
        $this->callDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->duration = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Call
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Call
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Call
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set callDateTime
     *
     * @param \DateTime|null $callDateTime
     * @return Call
     */
    public function setCallDateTime(?\DateTime $callDateTime = null)
    {
        $this->callDateTime = $callDateTime;

        return $this;
    }

    /**
     * Get callDateTime
     */
    public function getCallDateTime(): ?\DateTime
    {
        return $this->callDateTime;
    }

    /**
     * Set duration
     *
     * @param int $duration
     * @return Call
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set direction
     *
     * @param CallDirection $direction
     * @return Call
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return CallDirection
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set owner
     *
     * @param User $owner
     * @return Call
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set callStatus
     *
     * @param CallStatus $callStatus
     * @return Call
     */
    public function setCallStatus($callStatus)
    {
        $this->callStatus = $callStatus;

        return $this;
    }

    /**
     * Get callStatus
     *
     * @return CallStatus
     */
    public function getCallStatus()
    {
        return $this->callStatus;
    }

    /**
     * @return \DateTime
     */
    #[\Override]
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     * @return $this
     */
    #[\Override]
    public function setCreatedAt(?\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    #[\Override]
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     *
     * @return $this
     */
    #[\Override]
    public function setUpdatedAt(?\DateTime $updatedAt = null)
    {
        $this->updatedAtSet = false;
        if ($updatedAt !== null) {
            $this->updatedAtSet = true;
        }

        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isUpdatedAtSet()
    {
        return $this->updatedAtSet;
    }

    /**
     * Set organization
     *
     * @param Organization|null $organization
     * @return Call
     */
    public function setOrganization(?Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    #[\Override]
    public function __toString()
    {
        return (string)$this->getSubject();
    }
}
