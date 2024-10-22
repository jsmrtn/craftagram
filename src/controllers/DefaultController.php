<?php
/**
 * craftagram plugin for Craft CMS 4.x / 5.x
 *
 * Grab Instagram content through the Instagram API

 * @copyright Copyright (c) 2024 Joshua Martin
 */

namespace jsmrtn\craftagram\controllers;

use jsmrtn\craftagram\Craftagram;
use jsmrtn\craftagram\services\CraftagramService;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use yii\web\UnauthorizedHttpException;

/**
 * @author    Joshua Martin
 * @package   Craftagram
 * @since     1.0.0
 */
class DefaultController extends Controller {

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|int|bool $allowAnonymous = ['refresh-token', 'auth', 'get-next-page', 'api'];

    // Public Methods
    // =========================================================================
    
    /**
      * Refresh the instragram token for all enabled sites
      * or for a specific one if param siteId is given
      *
      * @param  integer $siteId siteId to refresh the long access token for
      * @return bool true if successful, otherwise false
      */
      public function actionRefreshToken() {
        $siteId = Craft::$app->getRequest()->getParam('siteId', null);

        if ($siteId) {
            return Craftagram::$plugin->craftagramService->refreshTokenForSiteId((int) $siteId);
        }

        return Craftagram::$plugin->craftagramService->refreshToken();
    }
    /**
     * Redirect user to craftagram settings page
     *
     * @return Response|null
     */
    public function actionAuth() {
        $url = parse_url(rtrim(Craft::parseEnv(Craft::$app->sites->primarySite->baseUrl), '/') . $_SERVER['REQUEST_URI']); 
        parse_str($url['query'], $params); 
        $code = $params['code'];
        $siteId = $params['state'];

        if ($code != '') {
            $getToken = Craftagram::$plugin->craftagramService->getShortAccessToken($code, $siteId);
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('craftagram/settings/' . $siteId))->send();
            exit;
        }
    }

    /**
     * Return instagram feed based on supplied page
     *
     * @return string
     */
    public function actionGetNextPage($url, $siteId, $limit = 25) {
        $url = parse_url($url);

        if (array_key_exists('query', $url)) {
            parse_str($url['query'], $params);
            $after = $params['after'];
            $limit = $params['limit'];
        } else {
            $after = $url['path'];
        }

        return json_encode(Craftagram::$plugin->craftagramService->getInstagramFeed($limit, $siteId, $after));
    }

    /**
     * Return instagram feed based on supplied page
     *
     * @return string
     */
    public function actionApi($limit = 25, $siteId = 0, $url = '') {
        $isSecured = Craftagram::$plugin->craftagramService->checkIfSecured($siteId);

        if ($isSecured) {
            if (!Craftagram::$plugin->craftagramService->handleAuthentication()) {
                throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
            }
        }

        header('Content-type:application/json;charset=utf-8');
        echo json_encode(Craftagram::$plugin->craftagramService->getInstagramFeed($limit, $siteId, $url));
        die();
    }
}