<?php

namespace scaramangagency\craftagram\migrations;

use Craft;
use craft\db\Migration;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\records\SettingsRecord as SettingsRecord;

/**
 * m210121_165738_secureapi migration.
 */
class m210121_165738_secureapi extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        echo "m210121_165738_secureapi updating.\n";
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
        echo "m210121_165738_secureapi cannot be reverted.\n";
        return false;
    }
}
