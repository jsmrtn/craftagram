<?php

namespace jsmrtn\craftagram\migrations;

use Craft;
use craft\db\Migration;

use jsmrtn\craftagram\Craftagram;
use jsmrtn\craftagram\records\SettingsRecord as SettingsRecord;

/**
 * m201127_131603_multisite migration.
 */
class m201127_131603_multisite extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        echo "m201127_131603_multisite updating.\n";
        if (Craft::$app->db->schema->getTableSchema('{{%craftagram_settings}}') != null) {
            $this->addColumn(
                '{{%craftagram_settings}}',
                'craftagramSiteId',
                'integer AFTER longAccessToken'
            );
            $this->addColumn(
                '{{%craftagram_settings}}',
                'appSecret',
                'text AFTER id'
            );
            $this->addColumn(
                '{{%craftagram_settings}}',
                'appId',
                'text AFTER id'
            );
            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%craftagram_settings}}', 'craftagramSiteId'),
                '{{%craftagram_settings}}',
                'craftagramSiteId',
                '{{%sites}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
            
            $this->update(
                '{{%craftagram_settings}}', 
                [
                    'craftagramSiteId' => Craft::$app->sites->primarySite->id,
                    'appSecret' => Craftagram::$plugin->getSettings()->appSecret,
                    'appId' => Craftagram::$plugin->getSettings()->appId
                ],
                [
                    'id' => 1
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m201127_131603_multisite cannot be reverted.\n";
        return false;
    }
}
