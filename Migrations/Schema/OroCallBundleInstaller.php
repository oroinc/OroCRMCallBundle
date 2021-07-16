<?php

namespace Oro\Bundle\CallBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCallBundleInstaller implements Installation, ActivityExtensionAwareInterface, CommentExtensionAwareInterface
{
    /** @var CommentExtension */
    protected $comment;

    /** @var ActivityExtension */
    protected $activityExtension;

    public function setCommentExtension(CommentExtension $commentExtension)
    {
        $this->comment = $commentExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_10';
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createOrocrmCallTable($schema);
        $this->createOrocrmCallDirectionTable($schema);
        $this->createOrocrmCallStatusTable($schema);

        self::createCallDirectionTranslationTable($schema);
        self::createCallStatusTranslationTable($schema);

        /** Foreign keys generation **/
        $this->addOrocrmCallForeignKeys($schema);

        $this->comment->addCommentAssociation($schema, 'orocrm_call');
    }

    /**
     * Create orocrm_call table
     */
    protected function createOrocrmCallTable(Schema $schema)
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
        $table->addColumn('call_date_time', 'datetime', []);
        $table->addColumn('duration', 'duration', ['notnull' => false, 'default' => null]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['organization_id'], 'IDX_1FBD1A2432C8A3DE', []);
        $table->addIndex(['owner_id'], 'IDX_1FBD1A247E3C61F9', []);
        $table->addIndex(['call_status_name'], 'IDX_1FBD1A2476DB3689', []);
        $table->addIndex(['call_direction_name'], 'IDX_1FBD1A249F3E257D', []);
        $table->addIndex(['call_date_time', 'id'], 'call_dt_idx');

        $this->activityExtension->addActivityAssociation($schema, 'orocrm_call', 'oro_user');
    }

    /**
     * Create orocrm_call_direction table
     */
    protected function createOrocrmCallDirectionTable(Schema $schema)
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
    protected function createOrocrmCallStatusTable(Schema $schema)
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
    protected function addOrocrmCallForeignKeys(Schema $schema)
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
     * Generate table orocrm_call_direction_trans
     */
    public static function createCallDirectionTranslationTable(Schema $schema)
    {
        /** Generate table orocrm_call_direction_trans **/
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
            'call_direction_translation_idx',
            []
        );
        /** End of generate table orocrm_call_direction_trans **/
    }

    /**
     * Generate table orocrm_call_status_trans
     */
    public static function createCallStatusTranslationTable(Schema $schema)
    {
        /** Generate table orocrm_call_status_trans **/
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
            'call_status_translation_idx',
            []
        );
        /** End of generate table orocrm_call_status_trans **/
    }
}
