<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\controllers;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\services\CraftagramService;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;

/**
 * @author    Scaramanga Agency
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
    protected $allowAnonymous = ['refresh-token', 'auth', 'get-next-page'];

    public function actionRefreshToken() {
        return Craftagram::$plugin->craftagramService->refreshToken();
    }

    public function actionHandleAuth($client_id) {
        $url = rtrim(Craft::parseEnv(Craft::$app->sites->primarySite->baseUrl), '/'); 
        $appId = Craft::parseEnv($client_id);

        Craft::$app->getResponse()->redirect('https://api.instagram.com/oauth/authorize?client_id='.$appId.'&redirect_uri='.$url.'/actions/craftagram/default/auth&scope=user_profile,user_media&response_type=code')->send();
        exit;
    }

    public function actionAuth() {
        $url = parse_url(Craft::parseEnv(Craft::$app->sites->primarySite->baseUrl) . $_SERVER['REQUEST_URI']); 
        parse_str($url['query'], $params); 
        $code = $params['code'];

        if ($code != '') {
            $getToken = Craftagram::$plugin->craftagramService->getShortAccessToken($code);
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('craftagram/settings'))->send();
            exit;
        }
    }

    public function actionGetNextPage($url) {
        $url = parse_url($url);
        parse_str($url['query'], $params);
        $after = $params['after'];
        $limit = $params['limit'];

        return json_encode(Craftagram::$plugin->craftagramService->getInstagramFeed($limit, $after));
    }
}