<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema\v1_12;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\CallBundle\Entity\Call;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManagerAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManagerAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Fully hides externalId field of Call entity from grid, filters, view and edit form.
 */
class HideCallExternalIdField implements Migration, ExtendOptionsManagerAwareInterface
{
    use ExtendOptionsManagerAwareTrait;

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        // Works in case when the affected field does not yet exist.
        $this->extendOptionsManager->mergeColumnOptions(
            'orocrm_call',
            'external_id',
            [
                'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_FALSE],
                'form' => ['is_enabled' => false],
                'view' => ['is_displayable' => false],
            ]
        );

        // Works in case when the affected field already exists.
        $queries->addPostQuery(new UpdateEntityConfigFieldValueQuery(
            Call::class,
            'external_id',
            'datagrid',
            'is_visible',
            DatagridScope::IS_VISIBLE_FALSE
        ));
        $queries->addPostQuery(new UpdateEntityConfigFieldValueQuery(
            Call::class,
            'external_id',
            'form',
            'is_enabled',
            false
        ));
        $queries->addPostQuery(new UpdateEntityConfigFieldValueQuery(
            Call::class,
            'external_id',
            'view',
            'is_displayable',
            false
        ));
    }
}
