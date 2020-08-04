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

    public function actionIndex() {
        $settings = Craftagram::$plugin->getSettings();
        $longAccessToken = Craftagram::$plugin->craftagramService->getLongAccessTokenSetting();
        
        return $this->renderTemplate('craftagram/settings', [
            'settings' => $settings,
            'longAccessToken' => $longAccessToken
        ]);
    }

    public function actionSavePluginSettings() {
        $this->requirePostRequest();
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);
        $plugin = Craft::$app->getPlugins()->getPlugin('craftagram');

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        // Save ID/Secret
        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));

            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin
            ]);

            return null;
        }

        // Save Long Access Token
        $longAccessTokenRecord = SettingsRecord::findOne(1);
            
        if (!$longAccessTokenRecord) {
            $longAccessTokenRecord = new SettingsRecord();
        }

        $longAccessTokenRecord->setAttribute('longAccessToken', $settings['longAccessToken']);
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
