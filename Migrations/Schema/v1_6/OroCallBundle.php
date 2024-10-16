<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema\v1_6;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCallBundle implements Migration
{
    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->createCallDirectionTranslationTable($schema);
        $this->createCallStatusTranslationTable($schema);
    }

    private function createCallDirectionTranslationTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call_direction_trans');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 32]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'call_direction_translation_idx'
        );
    }

    private function createCallStatusTranslationTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call_status_trans');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 32]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'call_status_translation_idx'
        );
    }
}
