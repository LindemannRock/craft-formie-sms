<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\models;

use craft\base\Model;
use lindemannrock\base\traits\SettingsDisplayNameTrait;

/**
 * Formie SMS Settings Model
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     1.0.0
 */
class Settings extends Model
{
    use SettingsDisplayNameTrait;

    /**
     * @var string The plugin display name
     */
    public string $pluginName = 'Formie SMS';

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return [
            ['pluginName', 'required'],
            ['pluginName', 'string'],
        ];
    }
}
