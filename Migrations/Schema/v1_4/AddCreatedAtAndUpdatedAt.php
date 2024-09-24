<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddCreatedAtAndUpdatedAt implements Migration, OrderedMigrationInterface
{
    #[\Override]
    public function getOrder()
    {
        return 1;
    }

    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_call');
        $table->addColumn('created_at', 'datetime', ['notnull' => false]);
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
    }
}
