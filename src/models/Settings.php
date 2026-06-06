<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\models;

use craft\base\Model;
use lindemannrock\base\helpers\SettingsPostHelper;
use lindemannrock\base\traits\PluginNameSettingsTrait;
use lindemannrock\base\traits\SettingsConfigTrait;
use lindemannrock\base\traits\SettingsDisplayNameTrait;

/**
 * Formie SMS Settings Model
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     3.0.0
 */
class Settings extends Model
{
    use PluginNameSettingsTrait;
    use SettingsConfigTrait;
    use SettingsDisplayNameTrait;

    /**
     * @var array<string, array<int, string>>
     */
    private array $settingsPostErrors = [];

    /**
     * @var string The name of the plugin as it appears in the Control Panel menu
     */
    public string $pluginName = 'Formie SMS';

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true): void
    {
        if (!is_array($values)) {
            parent::setAttributes($values, $safeOnly);
            return;
        }

        $this->settingsPostErrors = [];

        $result = SettingsPostHelper::apply(
            model: $this,
            postedValues: $values,
            allowedAttributes: $this->settingsPostAttributes(),
        );

        if ($result->hasErrors) {
            $this->settingsPostErrors = $this->getErrors();
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->settingsPostErrors !== []) {
            foreach ($this->settingsPostErrors as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->addError($attribute, $error);
                }
            }

            $this->settingsPostErrors = [];
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return $this->pluginNameSettingsRules();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return $this->pluginNameSettingsLabel();
    }

    /**
     * Plugin handle for config file resolution
     */
    protected static function pluginHandle(): string
    {
        return 'formie-sms';
    }

    /**
     * @return array<int, string>
     */
    private function settingsPostAttributes(): array
    {
        return [
            'pluginName',
        ];
    }
}
