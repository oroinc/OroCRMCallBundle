<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema\v1_9;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Updated object_class column length according to
 * {@see \Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation::$objectClass} field metadata change
 */
class UpdateObjectClassFieldLength implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('orocrm_call_direction_trans');
        $table->changeColumn('object_class', ['length' => 191]);

        $table = $schema->getTable('orocrm_call_status_trans');
        $table->changeColumn('object_class', ['length' => 191]);
    }
}
