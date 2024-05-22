<?php

namespace jsmrtn\craftagram\migrations;

use Craft;
use craft\db\Migration;

use jsmrtn\craftagram\Craftagram;
use jsmrtn\craftagram\records\SettingsRecord as SettingsRecord;

/**
 * m200930_110509_accessTokenLength migration.
 */
class m200930_110509_accessTokenLength extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        
        echo "m200930_110509_accessTokenLength updating.\n";
        if (Craft::$app->db->schema->getTableSchema('{{%craftagram_settings}}') != null) {
            $this->alterColumn(
                '{{%craftagram_settings}}',
                'longAccessToken',
                'text'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200930_110509_accessTokenLength cannot be reverted.\n";
        return false;
    }
}