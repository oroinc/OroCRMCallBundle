<?php

namespace Oro\Bundle\CallBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CallBundle\Entity\CallDirection;

/**
 * Fixture for loading call direction reference data.
 *
 * Loads predefined {@see CallDirection} entities (incoming and outgoing).
 */
class LoadCallDirectionData extends AbstractFixture
{
    /**
     * @var array
     */
    protected $data = array(
        'incoming' => 'Incoming',
        'outgoing' => 'Outgoing',
    );

    #[\Override]
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $name => $label) {
            $callDirection = new CallDirection($name);
            $callDirection->setLabel($label);
            $manager->persist($callDirection);
        }

        $manager->flush();
    }
}
