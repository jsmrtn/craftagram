<?php

namespace scaramangagency\craftagram\controllers;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\services\CraftagramService;
use scaramangagency\craftagram\records\SettingsRecord as SettingsRecord;

use Craft;
use craft\web\Controller;

class SettingsController extends Controller
{

    // Public Methods
    // =========================================================================

    public function actionIndex(int $siteId = 0) {
        if ($siteId == 0) {
            $settingsRecord = SettingsRecord::findOne(1);
            $siteId = Craft::$app->sites->primarySite->id;
        } else {
            $params = [ 
                'craftagramSiteId' => $siteId
            ];

            $settingsRecord = SettingsRecord::findOne($params);
        }

        if (!$settingsRecord) {
            $settingsRecord = new SettingsRecord();
        }

        return $this->renderTemplate('craftagram/settings', [
            'siteId' => $siteId,
            'settings' => $settingsRecord
        ]);
    }

    public function actionSavePluginSettings() {
        $this->requirePostRequest();
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);
        $plugin = Craft::$app->getPlugins()->getPlugin('craftagram');

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        $params = [ 
            'craftagramSiteId' => $settings['siteId']
        ];

        // Save Long Access Token
        $longAccessTokenRecord = SettingsRecord::findOne($params);
            
        if (!$longAccessTokenRecord) {
            $longAccessTokenRecord = new SettingsRecord();
        }

        $longAccessTokenRecord->setAttribute('appId', $settings['appId']);
        $longAccessTokenRecord->setAttribute('appSecret', $settings['appSecret']);
        $longAccessTokenRecord->setAttribute('longAccessToken', $settings['longAccessToken']);
        $longAccessTokenRecord->setAttribute('craftagramSiteId', $settings['siteId']);

        if (!$longAccessTokenRecord->save()) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));

            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin
            ]);

            return null;
        }


        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));
        return $this->redirectToPostedUrl();
    }

}
