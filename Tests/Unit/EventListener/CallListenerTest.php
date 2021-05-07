<?php

namespace Oro\Bundle\CallBundle\Tests\Unit\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\CallBundle\EventListener\Datagrid\CallListener;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datagrid\ParameterBag;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\UserBundle\Entity\User;

class CallListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var EntityManager|\PHPUnit\Framework\MockObject\MockObject */
    private $entityManager;

    /** @var CallListener */
    private $listener;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);

        $this->listener = new CallListener($this->entityManager);
    }

    /**
     * @dataProvider onBuildBeforeDataProvider
     */
    public function testOnBuildBefore(array $parameters, array $expectedUnsets = [])
    {
        $config = $this->createMock(DatagridConfiguration::class);
        if ($expectedUnsets) {
            $with = [];
            foreach ($expectedUnsets as $value) {
                $with[] = [$value];
            }
            $config->expects($this->exactly(count($expectedUnsets)))
                ->method('offsetUnsetByPath')
                ->withConsecutive(...$with);
        } else {
            $config->expects($this->never())
                ->method('offsetUnsetByPath');
        }

        $dataGrid = $this->createMock(DatagridInterface::class);
        $dataGrid->expects($this->any())
            ->method('getParameters')
            ->willReturn($this->createParameterBag($parameters));

        $this->listener->onBuildBefore(new BuildBefore($dataGrid, $config));
    }

    public function onBuildBeforeDataProvider(): array
    {
        return [
            'no filters' => [
                'parameters' => [],
            ],
            'filter by contact' => [
                'parameters' => [
                    'contactId' => 1,
                ],
                'expectedUnsets' => [
                    '[columns][contactName]',
                    '[filters][columns][contactName]',
                    '[sorters][columns][contactName]',
                ],
            ],
            'filter by account' => [
                'parameters' => [
                    'accountId' => 1,
                ],
                'expectedUnsets' => [
                    '[columns][accountName]',
                    '[filters][columns][accountName]',
                    '[sorters][columns][accountName]',
                ],
            ],
        ];
    }

    public function testOnBuildAfterWithoutParameters()
    {
        $parameters = [];
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->entityManager->expects($this->never())
            ->method('find');

        $queryBuilder->expects($this->never())
            ->method($this->anything());

        $this->listener->onBuildAfter($this->createBuildAfterEvent($queryBuilder, $parameters));
    }

    public function testOnBuildAfterFilterByUser()
    {
        $parameters = [
            'userId' => 12
        ];
        $user = new User();
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with('OroUserBundle:User', 12)
            ->willReturn($user);

        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('call.owner = :user')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('user', $this->identicalTo($user))
            ->willReturnSelf();

        $this->listener->onBuildAfter($this->createBuildAfterEvent($queryBuilder, $parameters));
    }

    private function createBuildAfterEvent(QueryBuilder $queryBuilder, array $parameters): BuildAfter
    {
        $ormDataSource = $this->createMock(OrmDatasource::class);
        $ormDataSource->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder);

        $dataGrid = $this->createMock(DatagridInterface::class);
        $dataGrid->expects($this->once())
            ->method('getDatasource')
            ->willReturn($ormDataSource);
        $dataGrid->expects($this->once())
            ->method('getParameters')
            ->willReturn($this->createParameterBag($parameters));

        return new BuildAfter($dataGrid);
    }

    private function createParameterBag(array $data): ParameterBag
    {
        $parameters = $this->createMock(ParameterBag::class);
        $parameters->expects($this->any())
            ->method('has')
            ->willReturnCallback(function ($key) use ($data) {
                return isset($data[$key]);
            });
        $parameters->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($key) use ($data) {
                return $data[$key];
            });

        return $parameters;
    }
}
