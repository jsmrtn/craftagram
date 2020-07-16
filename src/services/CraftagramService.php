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
use scaramangagency\craftagram\records\SettingsRecord as SettingsRecord;

use Craft;
use craft\base\Component;
use craft\services\Plugins;
use putyourlightson\logtofile\LogToFile;

/**
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.0.0
 */
class CraftagramService extends Component {

    public function getLongAccessTokenSetting() {
        $longAccessTokenRecord = SettingsRecord::findOne(1); 

        if (!$longAccessTokenRecord) {
            LogToFile::info('An access token has not been obtained from Instagram', 'craftagram');
            return false;
        }

        return $longAccessTokenRecord->getAttribute('longAccessToken');
    }

    public function refreshToken() {
        $longAccessTokenRecord = Craftagram::$plugin->craftagramService->getLongAccessTokenSetting();

        if (!$longAccessTokenRecord) {
            return false;
        }

        $ch = curl_init();
            
        $params = [
            'access_token' => $longAccessTokenRecord,
            'grant_type' => 'ig_refresh_token'
        ];

        curl_setopt($ch, CURLOPT_URL,'https://graph.instagram.com/refresh_access_token?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);

        try {
            $expires = json_decode($res)->expires_in;
            LogToFile::info('Successfully refreshed authentication token. Expires in ' . $expires, 'craftagram');
        } catch (Exception $e) {
            LogToFile::error('Failed to refresh authentication token. Error: ' . $res, 'craftagram');
        }

        return true;
    }

    public function getShortAccessToken($code) {
        $ch = curl_init();
        
        $params = [
            'client_id' => Craftagram::$plugin->getSettings()->appId,
            'client_secret' => Craftagram::$plugin->getSettings()->appSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => rtrim(Craft::parseEnv(Craft::$app->sites->primarySite->baseUrl), '/') . '/actions/craftagram/default/auth',
            'code' => $code
        ];

        curl_setopt($ch, CURLOPT_URL,'https://api.instagram.com/oauth/access_token');
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
            'client_secret' => Craftagram::$plugin->getSettings()->appSecret,
            'grant_type' => 'ig_exchange_token',
            'access_token' => $shortAccessToken
        ];

        curl_setopt($ch, CURLOPT_URL,'https://graph.instagram.com/access_token?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);
        $token = json_decode($res)->access_token;

        $plugin = Craft::$app->getPlugins()->getPlugin('craftagram');

        if ($plugin !== null) {
            $longAccessTokenRecord = SettingsRecord::findOne(1);
            
            if (!$longAccessTokenRecord) {
                $longAccessTokenRecord = new SettingsRecord();
            }

            $longAccessTokenRecord->setAttribute('longAccessToken', $token);
            $longAccessTokenRecord->save();
        }
        
        return $token;
    }

    
    public function getInstagramFeed($limit, $after) {
        $longAccessTokenRecord = Craftagram::$plugin->craftagramService->getLongAccessTokenSetting();

        if (!$longAccessTokenRecord) {
            return false;
        }

        $ch = curl_init();

        $params = [
            'fields' => 'caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username',
            'access_token' => $longAccessTokenRecord,
            'limit' => $limit
        ];

        if ($after != '') {
            $params['after'] = $after;
        }

        curl_setopt($ch, CURLOPT_URL,'https://graph.instagram.com/me/media?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res);
        return (isset($res->data) ? $res : null);
    }

    public function getProfileMeta($username) {
        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,'https://www.instagram.com/'.$username.'/?__a=1');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $res = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($res);
            
            $meta = null;

            if (isset($res->graphql)) {
                if (isset($res->graphql->user)) {
                    $meta = [
                        'profile_picture' => $res->graphql->user->profile_pic_url,
                        'profile_picture_hd' => $res->graphql->user->profile_pic_url_hd,
                        'followers' => $res->graphql->user->edge_followed_by->count,
                        'following' => $res->graphql->user->edge_follow->count,
                    ];
                }
            }

            return $meta;

        } catch (Exception $e) {
            LogToFile::error('Failed to get profile meta. This endpoint may no longer be available.', 'craftagram');
            return null;
        }
    }

    
}
