<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareTrait;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCallBundleInstaller implements Installation, ActivityExtensionAwareInterface, CommentExtensionAwareInterface
{
    use ActivityExtensionAwareTrait;
    use CommentExtensionAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function getMigrationVersion(): string
    {
        return 'v1_10';
    }

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        /** Tables generation **/
        $this->createOrocrmCallTable($schema);
        $this->createOrocrmCallDirectionTable($schema);
        $this->createOrocrmCallStatusTable($schema);
        $this->createCallDirectionTranslationTable($schema);
        $this->createCallStatusTranslationTable($schema);

        /** Foreign keys generation **/
        $this->addOrocrmCallForeignKeys($schema);

        $this->commentExtension->addCommentAssociation($schema, 'orocrm_call');
    }

    /**
     * Create orocrm_call table
     */
    private function createOrocrmCallTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('call_direction_name', 'string', ['notnull' => false, 'length' => 32]);
        $table->addColumn('call_status_name', 'string', ['notnull' => false, 'length' => 32]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('subject', 'string', ['length' => 255]);
        $table->addColumn('phone_number', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('notes', 'text', ['notnull' => false]);
        $table->addColumn('call_date_time', 'datetime');
        $table->addColumn('duration', 'duration', ['notnull' => false, 'default' => null]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['organization_id'], 'IDX_1FBD1A2432C8A3DE');
        $table->addIndex(['owner_id'], 'IDX_1FBD1A247E3C61F9');
        $table->addIndex(['call_status_name'], 'IDX_1FBD1A2476DB3689');
        $table->addIndex(['call_direction_name'], 'IDX_1FBD1A249F3E257D');
        $table->addIndex(['call_date_time', 'id'], 'call_dt_idx');

        $this->activityExtension->addActivityAssociation($schema, 'orocrm_call', 'oro_user');
    }

    /**
     * Create orocrm_call_direction table
     */
    private function createOrocrmCallDirectionTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call_direction');
        $table->addColumn('name', 'string', ['length' => 32]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->setPrimaryKey(['name']);
        $table->addUniqueIndex(['label'], 'UNIQ_D0EB34BAEA750E8');
    }

    /**
     * Create orocrm_call table
     */
    private function createOrocrmCallStatusTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call_status');
        $table->addColumn('name', 'string', ['length' => 32]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->setPrimaryKey(['name']);
        $table->addUniqueIndex(['label'], 'UNIQ_FBA13581EA750E8');
    }

    /**
     * Create orocrm_call table
     */
    private function addOrocrmCallForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('orocrm_call');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_call_direction'),
            ['call_direction_name'],
            ['name'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_call_status'),
            ['call_status_name'],
            ['name'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Create orocrm_call_direction_trans table
     */
    private function createCallDirectionTranslationTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call_direction_trans');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 32]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 16]);
        $table->addColumn('object_class', 'string', ['length' => 191]);
        $table->addColumn('field', 'string', ['length' => 32]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'call_direction_translation_idx'
        );
    }

    /**
     * Create orocrm_call_status_trans table
     */
    private function createCallStatusTranslationTable(Schema $schema): void
    {
        $table = $schema->createTable('orocrm_call_status_trans');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 32]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 16]);
        $table->addColumn('object_class', 'string', ['length' => 191]);
        $table->addColumn('field', 'string', ['length' => 32]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'call_status_translation_idx'
        );
    }
}
