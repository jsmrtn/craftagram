<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\assetbundles\Craftagram;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.0.0
 */
class CraftagramAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@scaramangagency/craftagram/assetbundles/craftagram/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Craftagram.js',
        ];

        $this->css = [
            'css/Craftagram.css',
        ];

        parent::init();
    }
}
