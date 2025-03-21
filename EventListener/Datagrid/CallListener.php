<?php

namespace Oro\Bundle\CallBundle\EventListener\Datagrid;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Add required filters and remove useless fields in case of filtering
 */
class CallListener
{
    public function __construct(
        private ManagerRegistry $doctrine
    ) {
    }

    /**
     * Remove useless fields in case of filtering
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        $parameters = $event->getDatagrid()->getParameters();

        if ($parameters->has('contactId')) {
            $this->removeColumn($config, 'contactName');
        }

        if ($parameters->has('accountId')) {
            $this->removeColumn($config, 'accountName');
        }
    }

    /**
     * Add required filters
     */
    public function onBuildAfter(BuildAfter $event)
    {
        $datagrid = $event->getDatagrid();
        /** @var OrmDatasource $ormDataSource */
        $ormDataSource = $datagrid->getDatasource();
        $queryBuilder = $ormDataSource->getQueryBuilder();
        $parameters = $datagrid->getParameters();

        if ($parameters->has('userId')) {
            $user = $this->doctrine->getManagerForClass(User::class)->find(User::class, $parameters->get('userId'));
            $queryBuilder
                ->andWhere('call.owner = :user')
                ->setParameter('user', $user);
        }

        if ($parameters->has('callIds')) {
            $callIds = $parameters->get('callIds');
            if (!is_array($callIds)) {
                $callIds = explode(',', $callIds);
            }
            $queryBuilder->andWhere($queryBuilder->expr()->in('call.id', ':callIds'))
                ->setParameter('callIds', $callIds);
        }
    }

    private function removeColumn(DatagridConfiguration $config, string $fieldName): void
    {
        $config->offsetUnsetByPath(sprintf('[columns][%s]', $fieldName));
        $config->offsetUnsetByPath(sprintf('[filters][columns][%s]', $fieldName));
        $config->offsetUnsetByPath(sprintf('[sorters][columns][%s]', $fieldName));
    }
}
