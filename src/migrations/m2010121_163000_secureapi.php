<?php

namespace scaramangagency\craftagram\migrations;

use Craft;
use craft\db\Migration;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\records\SettingsRecord as SettingsRecord;

/**
 * m2010121_163000_secureapi migration.
 */
class m2010121_163000_secureapi extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        echo "m2010121_163000_secureapi updating.\n";
        if (Craft::$app->db->schema->getTableSchema('{{%craftagram_settings}}') != null) {
            $this->addColumn(
                '{{%craftagram_settings}}',
                'secureApiEndpoint',
                'int AFTER craftagramSiteId'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m2010121_163000_secureapi cannot be reverted.\n";
        return false;
    }
}
