<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\console\controllers;

use scaramangagency\craftagram\Craftagram;

use craft\console\Controller;

/**
 * Token
 *
 * @author    dr. Ottó Radics
 * @package   Craftagram
 * @since     1.3.0
 */
class TokenController extends Controller {

    // Public Methods
    // =========================================================================

    /**
     * Refreshes Instagram Token.
     */
    public function actionIndex()
    {
        return Craftagram::$plugin->craftagramService->refreshToken();
    }
}
