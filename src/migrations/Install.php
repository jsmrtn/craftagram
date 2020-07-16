<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\migrations;

use scaramangagency\craftagram\Craftagram;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Class Craftagram
 *
 * @author    Scaramanga Agency
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
                    'longAccessToken'   => $this->text(),
                    'dateCreated'       => $this->dateTime()->notNull(),
                    'dateUpdated'       => $this->dateTime()->notNull(),
                    'uid'               => $this->uid()
                ]
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