<?php

namespace Oro\Bundle\CallBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CallBundle\Entity\CallStatus;

/**
 * Fixture for loading call status reference data.
 *
 * Loads predefined {@see CallStatus} entities (in progress and completed).
 */
class LoadCallStatusData extends AbstractFixture
{
    /**
     * @var array
     */
    protected $data = array(
        'in_progress' => 'In progress',
        'completed' => 'Completed',
    );

    #[\Override]
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $name => $label) {
            $callStatus = new CallStatus($name);
            $callStatus->setLabel($label);
            $manager->persist($callStatus);
        }

        $manager->flush();
    }
}
