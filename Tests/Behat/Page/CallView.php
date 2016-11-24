<?php

namespace Oro\Bundle\CallBundle\Tests\Behat\Page;

use Oro\Bundle\DataGridBundle\Tests\Behat\Element\Grid;
use Oro\Bundle\TestFrameworkBundle\Behat\Element\Page;

class CallView extends Page
{
    /**
     * {@inheritdoc}
     */
    public function open(array $parameters = [])
    {
        $this->getMainMenu()->openAndClick('Activities/Calls');
        $this->elementFactory->getPage()->getSession()->getDriver()->waitForAjax();

        /** @var Grid $grid */
        $grid = $this->elementFactory->createElement('Grid');
        $grid->clickActionLink($parameters['title'], 'View');
    }
}
