<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\records;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\StringHelper;

/**
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.2.0
 */
class SettingsRecord extends ActiveRecord {
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName(): string {
        return '{{%craftagram_settings}}';
    }
}