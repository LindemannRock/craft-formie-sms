<?php
/**
 * LindemannRock Formie SMS
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\tests\Stubs;

use lindemannrock\smsmanager\services\SmsService;

/**
 * Test stub for sms-manager's {@see SmsService}.
 *
 * The integration's `sendPayload()` dispatches every recipient through
 * `SmsManager::$plugin->sms->sendWithHandle(...)`. Letting that hit the
 * real service would attempt actual provider dispatch (DB inserts to the
 * sms-manager logs + analytics tables, possibly outbound HTTP) — neither
 * suitable nor deterministic for tests.
 *
 * This stub records every `sendWithHandle()` call so tests can assert on
 * the dispatched recipient list, message, sender handle, language, source
 * plugin, and source element id. The boolean return value is controllable
 * via {@see $returnValue} for failure-path coverage.
 *
 * Extends the real service so PHPStan's `@property SmsService $sms` on
 * SmsManager stays satisfied, and so the `Component` machinery (events,
 * behaviors, etc.) the parent provides is still available if anything
 * incidentally consults it.
 *
 * @since 3.10.0
 */
final class StubSmsService extends SmsService
{
    /**
     * Recorded calls to {@see sendWithHandle()}, in order. Each entry
     * captures the full positional tuple the integration passes through.
     *
     * @var list<array{to: string, message: string, senderIdHandle: string, language: string, sourcePlugin: ?string, sourceElementId: ?int, siteId: ?int}>
     */
    public array $sentCalls = [];

    /**
     * Value returned by every `sendWithHandle()` call. Defaults to true
     * (success); flip to false to exercise the integration's
     * `Integration::error()` failure-log branch.
     */
    public bool $returnValue = true;

    public function sendWithHandle(
        string $to,
        string $message,
        string $senderIdHandle,
        string $language = 'en',
        ?string $sourcePlugin = null,
        ?int $sourceElementId = null,
        ?int $siteId = null,
    ): bool {
        $this->sentCalls[] = [
            'to' => $to,
            'message' => $message,
            'senderIdHandle' => $senderIdHandle,
            'language' => $language,
            'sourcePlugin' => $sourcePlugin,
            'sourceElementId' => $sourceElementId,
            'siteId' => $siteId,
        ];

        return $this->returnValue;
    }
}
