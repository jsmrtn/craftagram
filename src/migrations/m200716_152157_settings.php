<?php

namespace scaramangagency\craftagram\migrations;

use Craft;
use craft\db\Migration;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\records\SettingsRecord as SettingsRecord;

/**
 * m200716_152157_settings migration.
 */
class m200716_152157_settings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        
        echo "m200716_152157_settings updating.\n";
        if (Craft::$app->db->schema->getTableSchema('{{%craftagram_settings}}') === null) {
            $this->createTable(
                '{{%craftagram_settings}}',
                [
                    'id'                => $this->primaryKey(),
                    'longAccessToken'   => $this->string(150),
                    'dateCreated'       => $this->dateTime()->notNull(),
                    'dateUpdated'       => $this->dateTime()->notNull(),
                    'uid'               => $this->uid()
                ]
            );

            if (Craftagram::$plugin->getSettings()->longAccessToken !== null) {
                $this->insert(
                    '{{%craftagram_settings}}',
                    [
                        'longAccessToken' => Craftagram::$plugin->getSettings()->longAccessToken
                    ]
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200716_152157_settings cannot be reverted.\n";
        return false;
    }
}
