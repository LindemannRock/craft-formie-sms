<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Support;

use craft\config\BaseConfig;
use craft\services\Config;

/**
 * Use only synthetic provider configuration and avoid DNS in intercepted tests.
 * @since 3.11.3
 */
final class SyntheticProviderConfig extends Config
{
    public function getConfigFromFile(string $filename): array|callable|BaseConfig
    {
        if ($filename === 'sms-manager') {
            return ['security' => ['blockPrivateNetworks' => false, 'allowedApiHosts' => ['gateway.example.test']]];
        }
        return parent::getConfigFromFile($filename);
    }
}
