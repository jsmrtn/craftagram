<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram\services;

use scaramangagency\craftagram\Craftagram;

use Craft;
use craft\base\Component;
use craft\services\Plugins;
use putyourlightson\logtofile\LogToFile;

/**
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.0.0
 */
class CraftagramService extends Component
{
    
    public function refreshToken() {
        $ch = curl_init();
            
        $params = [
            "access_token" => Craftagram::getInstance()->getSettings()->longAccessToken,
            "grant_type" => "ig_refresh_token"
        ];

        curl_setopt($ch, CURLOPT_URL,"https://graph.instagram.com/refresh_access_token?".http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);

        try {
            $expires = json_decode($res)->expires_in;
            LogToFile::info("Successfully refreshed authentication token. Expires in " . $expires, "craftagram");
        } catch (Exception $e) {
            LogToFile::error("Failed to refresh authentication token. Error: " . $res, "craftagram");
        }

        return true;
    }

    public function getShortAccessToken($code) {
        $ch = curl_init();
            
        $params = [
            "client_id" => Craftagram::$plugin->getSettings()->appId,
            "client_secret" => Craftagram::$plugin->getSettings()->appSecret,
            "grant_type" => "authorization_code",
            "redirect_uri" => Craft::parseEnv(Craft::$app->sites->primarySite->baseUrl) . "/actions/craftagram/default/auth",
            "code" => $code
        ];

        curl_setopt($ch, CURLOPT_URL,"https://api.instagram.com/oauth/access_token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);
        $shortAccessToken = json_decode($res)->access_token;

        return Craftagram::$plugin->craftagramService->getLongAccessToken($shortAccessToken);
    }


    public function getLongAccessToken($shortAccessToken) {
        $ch = curl_init();

        $params = [
            "client_secret" => Craftagram::$plugin->getSettings()->appSecret,
            "grant_type" => "ig_exchange_token",
            "access_token" => $shortAccessToken
        ];

        curl_setopt($ch, CURLOPT_URL,"https://graph.instagram.com/access_token?".http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);
        $token = json_decode($res)->access_token;

        $plugin = Craft::$app->getPlugins()->getPlugin('craftagram');

        if ($plugin !== null) {
            Craft::$app->getPlugins()->savePluginSettings($plugin, array("longAccessToken" => $token));
        }
        
        return $token;
    }


    public function getInstagramFeed($limit, $after) {
        $ch = curl_init();

        if ($after != "") {
            $params = [
                "fields" => "caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username",
                "access_token" => Craftagram::$plugin->getSettings()->longAccessToken,
                "limit" => $limit,
                "after" => $after
            ];
        } else {
            $params = [
                "fields" => "caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username",
                "access_token" => Craftagram::$plugin->getSettings()->longAccessToken,
                "limit" => $limit
            ];
    
        }

        curl_setopt($ch, CURLOPT_URL,"https://graph.instagram.com/me/media?".http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res);
        return (isset($res->data) ? $res : null);
    }
}
