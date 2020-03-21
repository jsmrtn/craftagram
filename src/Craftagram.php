<?php
/**
 * craftagram plugin for Craft CMS 3.x
 *
 * Grab Instagram content through the Instagram Basic Display API
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\craftagram;

use scaramangagency\craftagram\services\CraftagramService as CraftagramServiceService;
use scaramangagency\craftagram\variables\CraftagramVariable;
use scaramangagency\craftagram\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Craftagram
 *
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.0.0
 *
 * @property  CraftagramServiceService $craftagramService
 */
class Craftagram extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Craftagram
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['refreshToken'] = 'craftagram/refresh-token';
                $event->rules['auth'] = 'craftagram/default/auth';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftagram', CraftagramVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/craftagram'))->send();
                }
            }
        );
        Craft::info(
            Craft::t(
                'craftagram',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function afterSaveSettings()
    {
        parent::afterSaveSettings();
        Craft::$app->response
            ->redirect(UrlHelper::cpUrl('settings/plugins/craftagram'))
            ->send();
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'craftagram/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
