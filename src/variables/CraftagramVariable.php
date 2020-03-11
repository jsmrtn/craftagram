<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\variables;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\services\CraftagramService;

use Craft;

/**
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.0.0
 */
class CraftagramVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function getInstagramFeed($limit = 25, $url = "")
    {
        return CraftagramService::getInstagramFeed($limit, $url);
    }
}
