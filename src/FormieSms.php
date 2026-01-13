<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * SMS integration for Formie - Send SMS notifications on form submission via SMS Manager
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use lindemannrock\base\helpers\PluginHelper;
use lindemannrock\formiesms\integrations\miscellaneous\Sms;
use lindemannrock\formiesms\integrations\SmsManagerIntegration;
use lindemannrock\formiesms\models\Settings;
use lindemannrock\smsmanager\events\RegisterIntegrationsEvent as SmsManagerRegisterIntegrationsEvent;
use lindemannrock\smsmanager\services\IntegrationsService;
use verbb\formie\events\RegisterIntegrationsEvent;
use verbb\formie\services\Integrations;
use yii\base\Event;

/**
 * Formie SMS Plugin
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     1.0.0
 *
 * @property-read Settings $settings
 * @method Settings getSettings()
 */
class FormieSms extends Plugin
{
    /**
     * @var FormieSms|null Singleton plugin instance
     */
    public static ?FormieSms $plugin = null;

    /**
     * @var string Plugin schema version for migrations
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @var bool Whether the plugin exposes a control panel settings page
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Bootstrap the base plugin helper
        PluginHelper::bootstrap($this, 'formieSmsHelper');

        // Set the alias for this plugin
        Craft::setAlias('@lindemannrock/formiesms', __DIR__);
        Craft::setAlias('@formie-sms', __DIR__);

        // Register template roots
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['formie-sms'] = __DIR__ . '/templates';
            }
        );

        // Register the SMS integration with Formie
        Event::on(
            Integrations::class,
            Integrations::EVENT_REGISTER_INTEGRATIONS,
            function(RegisterIntegrationsEvent $event) {
                $event->miscellaneous[] = Sms::class;
            }
        );

        // Register with SMS Manager's integration system (for usage tracking)
        Event::on(
            IntegrationsService::class,
            IntegrationsService::EVENT_REGISTER_INTEGRATIONS,
            function(SmsManagerRegisterIntegrationsEvent $event) {
                $event->register('formie-sms', 'Formie SMS', SmsManagerIntegration::class);
            }
        );

        // Set the plugin name from settings
        $settings = $this->getSettings();
        if (!empty($settings->pluginName)) {
            $this->name = $settings->pluginName;
        }

        Craft::info(
            'Formie SMS plugin loaded',
            __METHOD__
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'formie-sms/settings',
            [
                'settings' => $this->getSettings(),
                'plugin' => $this,
            ]
        );
    }
}
