<?php

namespace Oro\Bundle\CallBundle\Tests\Behat\Page;

use Oro\Bundle\TestFrameworkBundle\Behat\Element\Page;

class CallCreate extends Page
{
    /**
     * {@inheritdoc}
     */
    public function open(array $parameters = [])
    {
        $this->getMainMenu()->openAndClick('Activities/Calls');
        $this->elementFactory->getPage()->getSession()->getDriver()->waitForAjax();
        $this->elementFactory->getPage()->clickLink('Log call');
    }
}
