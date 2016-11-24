<?php

namespace Oro\Bundle\CallBundle\Tests\Behat\Page;

use Oro\Bundle\DataGridBundle\Tests\Behat\Element\Grid;
use Oro\Bundle\TestFrameworkBundle\Behat\Element\Page;

class CallEdit extends Page
{
    /**
     * {@inheritdoc}
     */
    public function open(array $parameters = [])
    {
        $this->getMainMenu()->openAndClick('Activities/Calls');

        /** @var Grid $grid */
        $grid = $this->elementFactory->createElement('Grid');
        $grid->getSession()->getDriver()->waitForAjax();
        $grid->clickActionLink($parameters['title'], 'Edit');
    }
}
