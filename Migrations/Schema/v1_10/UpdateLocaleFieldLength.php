<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema\v1_10;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Updated 'locale' column length to be consistent with the data from 'oro_language' table
 */
class UpdateLocaleFieldLength implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_call_direction_trans');
        $table->changeColumn('locale', ['length' => 16]);

        $table = $schema->getTable('orocrm_call_status_trans');
        $table->changeColumn('locale', ['length' => 16]);
    }
}
