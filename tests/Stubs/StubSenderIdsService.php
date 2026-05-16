<?php
/**
 * LindemannRock Formie SMS
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\tests\Stubs;

use lindemannrock\smsmanager\records\SenderIdRecord;
use lindemannrock\smsmanager\services\SenderIdsService;

/**
 * Test stub for sms-manager's {@see SenderIdsService}.
 *
 * The integration's `resolveSenderIdHandle()` priority chain reads through
 * `SmsManager::$plugin->senderIds` for two of its three branches (legacy
 * `senderIdId` lookup and the empty-string "Use SMS Manager default"
 * sentinel). The real service reads from the install's saved settings +
 * sender_ids table, which makes the "default" branch non-deterministic
 * across dev installs. Swapping in this stub lets the priority-chain test
 * pin each branch to a configured return value rather than racing against
 * whatever the install happens to have configured at the time the suite
 * runs.
 *
 * Extends the real service (not just a duck-typed object) so PHPStan's
 * `@property SenderIdsService $senderIds` docblock on SmsManager stays
 * satisfied and any methods we don't override fall through to the real
 * implementation rather than returning null.
 *
 * @since 3.10.0
 */
final class StubSenderIdsService extends SenderIdsService
{
    /**
     * Record returned by {@see getDefaultSenderId()}. Null = "no default
     * configured", which is the input the priority chain treats as a fail
     * for the empty-string sentinel branch.
     */
    public ?SenderIdRecord $defaultSenderId = null;

    /**
     * Records returned by {@see getSenderIdById()}, keyed by id. Missing
     * key = "no such sender ID", which is what `resolveSenderIdHandle()`
     * sees when a pre-3.10 form references a deleted sender record.
     *
     * @var array<int, SenderIdRecord|null>
     */
    public array $byId = [];

    public function getSenderIdById(int $id): ?SenderIdRecord
    {
        return $this->byId[$id] ?? null;
    }

    public function getDefaultSenderId(int|string|null $providerIdOrHandle = null): ?SenderIdRecord
    {
        return $this->defaultSenderId;
    }
}
