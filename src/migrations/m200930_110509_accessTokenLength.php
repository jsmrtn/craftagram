<?php

namespace scaramangagency\craftagram\migrations;

use Craft;
use craft\db\Migration;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\records\SettingsRecord as SettingsRecord;

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