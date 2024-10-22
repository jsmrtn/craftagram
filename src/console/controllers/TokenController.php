<?php
/**
 * craftagram plugin for Craft CMS 4.x / 5.x
 *
 * Grab Instagram content through the Instagram API

 * @copyright Copyright (c) 2024 Joshua Martin
 */

namespace jsmrtn\craftagram\console\controllers;

use jsmrtn\craftagram\Craftagram;

use craft\console\Controller;
use yii\console\ExitCode;

class TokenController extends Controller {

    // Public Methods
    // =========================================================================

    /**
     * Refreshes Instagram long access token(s).
     *
     * @param  string|null $siteId if a siteId is given, update this site only
     * @return ExitCode 0 = OK, 1 = failed to refesh tokens for one or more sites
     */
    public function actionIndex(?int $siteId = null)
    {
        if ($siteId) {
            $sucess = Craftagram::$plugin->craftagramService->refreshTokenForSiteId($siteId);
        } else {
            $sucess = Craftagram::$plugin->craftagramService->refreshToken();
        }

        return $sucess
            ? ExitCode::OK
            : ExitCode::UNSPECIFIED_ERROR;
    }
}