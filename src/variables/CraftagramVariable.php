<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API

 * @copyright Copyright (c) 2024 Joshua Martin
 */

namespace jsmrtn\craftagram\variables;

use jsmrtn\craftagram\Craftagram;
use jsmrtn\craftagram\services\CraftagramService;

use Craft;

/**
 * @author    Joshua Martin
 * @package   Craftagram
 * @since     1.0.0
 */
class CraftagramVariable {

    // Public Methods
    // =========================================================================

    /**
     * Get instagram feed
     *
     * @return string
     */
    public function getInstagramFeed($limit = 25, $siteId = 0, $url = '') {
        return Craftagram::$plugin->craftagramService->getInstagramFeed($limit, $siteId, $url);
    }

    /**
     * Get profile meta information
     *
     * @return string
     */
    public function getProfileMeta($username) {
        return Craftagram::$plugin->craftagramService->getProfileMeta($username);
    }
}
