<?php

namespace Oro\Bundle\CallBundle\EventListener\Datagrid;

use Oro\Bundle\ActivityBundle\Manager\ActivityManager;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;

/**
 * Filters call activity datagrid results by target entity using {@see ActivityManager}
 * and {@see EntityRoutingHelper} to resolve entity classes from datagrid parameters.
 */
class ActivityGridListener
{
    /** @var ActivityManager */
    protected $activityManager;

    /** @var EntityRoutingHelper */
    protected $entityRoutingHelper;

    public function __construct(ActivityManager $activityManager, EntityRoutingHelper $entityRoutingHelper)
    {
        $this->activityManager     = $activityManager;
        $this->entityRoutingHelper = $entityRoutingHelper;
    }

    public function onBuildAfter(BuildAfter $event)
    {
        $datagrid   = $event->getDatagrid();
        $datasource = $datagrid->getDatasource();
        if ($datasource instanceof OrmDatasource) {
            $parameters = $datagrid->getParameters();
            $entityClass = $this->entityRoutingHelper->resolveEntityClass($parameters->get('entityClass'));
            $entityId = $parameters->get('entityId');

            // apply activity filter
            $this->activityManager->addFilterByTargetEntity(
                $datasource->getQueryBuilder(),
                $entityClass,
                $entityId
            );
        }
    }
}
