<?php

namespace jsmrtn\craftagram\migrations;

use Craft;
use craft\db\Migration;

/**
 * m241022_094125_embedUrl migration.
 */
class m241022_094125_embedUrl extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        echo "m241022_094125_embedUrl updating.\n";
        if (Craft::$app->db->schema->getTableSchema('{{%craftagram_settings}}') != null) {
            $this->addColumn(
                '{{%craftagram_settings}}',
                'embedUrl',
                'text AFTER longAccessToken'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m241022_094125_embedUrl cannot be reverted.\n";
        return false;
    }
}
