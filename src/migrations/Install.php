<?php
/**
 * craftagram plugin for Craft CMS 4.x / 5.x
 *
 * Grab Instagram content through the Instagram API

 * @copyright Copyright (c) 2024 Joshua Martin
 */

namespace jsmrtn\craftagram\migrations;

use jsmrtn\craftagram\Craftagram;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Class Craftagram
 *
 * @author    Joshua Martin
 * @package   Craftagram
 * @since     1.2.0
 *
 * @property  CraftagramServiceService $craftagramService
 */
class Install extends Migration {
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        return $this->createTables();
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables() {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%craftagram_settings}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%craftagram_settings}}',
                [
                    'id'                => $this->primaryKey(),
                    'appId'             => $this->text(),
                    'appSecret'         => $this->text(),
                    'longAccessToken'   => $this->text(),
                    'embedUrl'          => $this->text(),
                    'craftagramSiteId'  => $this->integer()->null(),
                    'secureApiEndpoint' => $this->integer()->defaultValue(1),
                    'dateCreated'       => $this->dateTime()->notNull(),
                    'dateUpdated'       => $this->dateTime()->notNull(),
                    'uid'               => $this->uid()
                ]
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
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function removeTables() {
        $this->dropTableIfExists('{{%craftagram_settings}}');
    }
}