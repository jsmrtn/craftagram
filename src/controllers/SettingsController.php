<?php

namespace scaramangagency\craftagram\controllers;

use scaramangagency\craftagram\Craftagram;
use scaramangagency\craftagram\services\CraftagramService;

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

        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));

        return $this->redirectToPostedUrl();
    }

}
