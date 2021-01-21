<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\models;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\services\CraftagramService;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $appId;
    public $appSecret;
    public $longAccessToken;
    public $craftagramSiteId;
    public $secureApiEndpoint;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['appId', 'appSecret'], 'required'],
            ['longAccessToken', 'string'],
            ['craftagramSiteId', 'integer'],
            ['secureApiEndpoint', 'boolean']
        ];
    }
}
