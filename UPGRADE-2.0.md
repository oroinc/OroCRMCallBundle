UPGRADE FROM 1.10 to 2.0
========================

#### SOAP API was removed
- removed all dependencies to the `besimple/soap-bundle` bundle. 
- removed SOAP annotations from the entities. Updated entities:
    - Oro\Bundle\CallBundle\Entity\Call
- removed classes:
    - Oro\Bundle\CallBundle\Controller\Api\Soap\CallController
    - Oro\Bundle\CallBundle\Tests\Functional\Controller\API\Soap\CallControllerTest
