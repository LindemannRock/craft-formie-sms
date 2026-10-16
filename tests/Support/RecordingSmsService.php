<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Support;

use lindemannrock\smsmanager\services\SmsService;

/**
 * Observe dispatch without replacing SMS Manager's provider or hold behavior.
 * @since 3.11.3
 */
final class RecordingSmsService extends SmsService
{
    public array $calls = [];
    public array $results = [];

    public function sendWithHandle(string $to, string $message, string $senderIdHandle, string $language = 'en', ?string $sourcePlugin = null, ?int $sourceElementId = null, ?int $siteId = null): bool
    {
        return $this->sendWithHandleDetails($to, $message, $senderIdHandle, $language, $sourcePlugin, $sourceElementId, $siteId)['success'];
    }

    public function sendWithHandleDetails(string $to, string $message, string $senderIdHandle, string $language = 'en', ?string $sourcePlugin = null, ?int $sourceElementId = null, ?int $siteId = null): array
    {
        $this->calls[] = compact('to', 'message', 'senderIdHandle', 'language', 'sourcePlugin', 'sourceElementId', 'siteId');
        return $this->results[] = parent::sendWithHandleDetails($to, $message, $senderIdHandle, $language, $sourcePlugin, $sourceElementId, $siteId);
    }
}
