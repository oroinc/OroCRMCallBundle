<?php

namespace Oro\Bundle\CallBundle\Tests\Functional\Environment;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\ActivityContactBundle\Direction\DirectionProviderInterface;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\CallBundle\Entity\CallDirection;
use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use Oro\Bundle\EntityBundle\Tests\Functional\Environment\TestEntityNameResolverDataLoaderInterface;

class TestEntityNameResolverDataLoader implements TestEntityNameResolverDataLoaderInterface
{
    private TestEntityNameResolverDataLoaderInterface $innerDataLoader;

    public function __construct(TestEntityNameResolverDataLoaderInterface $innerDataLoader)
    {
        $this->innerDataLoader = $innerDataLoader;
    }

    public function loadEntity(
        EntityManagerInterface $em,
        ReferenceRepository $repository,
        string $entityClass
    ): array {
        if (Call::class === $entityClass) {
            $call = new Call();
            $call->setOrganization($repository->getReference('organization'));
            $call->setOwner($repository->getReference('user'));
            $call->setDirection($em->getRepository(CallDirection::class)->findOneBy([
                'name' => DirectionProviderInterface::DIRECTION_OUTGOING
            ]));
            $call->setPhoneNumber('123-123');
            $call->setSubject('Test Call');
            $repository->setReference('call', $call);
            $em->persist($call);
            $em->flush();

            return ['call'];
        }

        return $this->innerDataLoader->loadEntity($em, $repository, $entityClass);
    }

    public function getExpectedEntityName(
        ReferenceRepository $repository,
        string $entityClass,
        string $entityReference,
        ?string $format,
        ?string $locale
    ): string {
        if (Call::class === $entityClass) {
            return EntityNameProviderInterface::SHORT === $format
                ? 'Test Call'
                : 'Test Call 123-123';
        }

        return $this->innerDataLoader->getExpectedEntityName(
            $repository,
            $entityClass,
            $entityReference,
            $format,
            $locale
        );
    }
}
