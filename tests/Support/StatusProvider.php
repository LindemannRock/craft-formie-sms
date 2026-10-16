<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Support;

use lindemannrock\smsmanager\providers\BaseProvider;

/**
 * Synthetic provider-owned lane projection for status-only tests.
 * @since 3.11.3
 */
final class StatusProvider extends BaseProvider
{
    public string $lane = 'main';
    public array $receivedSettings = [];

    public static function handle(): string
    {
        return 'synthetic-status';
    }
    public static function displayName(): string
    {
        return 'Synthetic';
    }
    public static function description(): string
    {
        return 'Synthetic';
    }
    public function validateSettings(array $settings): array
    {
        return [];
    }
    public function getTransportContext(array $settings): object
    {
        $this->receivedSettings = $settings;
        return (object)['activeLane' => $this->lane];
    }
    public function send(string $to, string $message, string $senderId, string $language, array $settings): array
    {
        throw new \LogicException('Status rendering must never send.');
    }
}
