<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API

 * @copyright Copyright (c) 2024 Joshua Martin
 */

namespace jsmrtn\craftagram\records;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\StringHelper;


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