<?php
/**
 * craftagram plugin for Craft CMS 4.x / 5.x
 *
 * Grab Instagram content through the Instagram API

 * @copyright Copyright (c) 2024 Joshua Martin
 */

namespace jsmrtn\craftagram\models;

use jsmrtn\craftagram\Craftagram;
use jsmrtn\craftagram\services\CraftagramService;

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
    public $embedUrl;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array {
        return [
            [['appId', 'appSecret', 'embedUrl'], 'required'],
            ['longAccessToken', 'string'],
            ['craftagramSiteId', 'integer'],
            ['secureApiEndpoint', 'boolean']
        ];
    }
}
