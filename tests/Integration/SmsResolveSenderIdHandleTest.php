<?php
/**
 * LindemannRock Formie SMS
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\tests\Integration;

use lindemannrock\formiesms\integrations\miscellaneous\Sms;
use lindemannrock\formiesms\tests\Stubs\StubSenderIdsService;
use lindemannrock\formiesms\tests\TestCase;
use lindemannrock\smsmanager\records\SenderIdRecord;

/**
 * Coverage for {@see Sms::resolveSenderIdHandle()} (3.10.0+).
 *
 * The integration's dispatch path is gated on a 3-step priority chain that
 * collapses `null`, empty-string sentinel ("Use SMS Manager default"), and
 * the legacy `?int $senderIdId` field into a single string handle (or null
 * for fail-loud). Every branch has a specific reason to exist — wrong-arm
 * routing here means a 3.10+ form silently sends from the wrong sender, or
 * a pre-3.10 form sends to no one. This pins each branch against the stub
 * so the priority chain stays exactly as documented in the source.
 *
 * The 'senderIds' component on sms-manager is swapped for a stub so the
 * default/by-id lookups are deterministic — the real service reads from
 * the install's saved settings + sender_ids table, which varies per dev
 * install.
 *
 * @since 3.10.0
 */
final class SmsResolveSenderIdHandleTest extends TestCase
{
    private StubSenderIdsService $stubSenderIds;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stubSenderIds = new StubSenderIdsService();
        $this->swapPluginComponent('sms-manager', 'senderIds', $this->stubSenderIds);
    }

    public function testExplicitNonEmptyHandleShortCircuitsToReturnedValue(): void
    {
        $sms = $this->makeSms();
        $sms->senderIdHandle = self::MARKER . 'explicit_handle';
        $sms->senderIdId = 9999; // Even with a legacy id present, explicit wins.

        // Stub deliberately empty: an explicit handle must not consult
        // SenderIdsService at all. If priority 1 ever regresses to a
        // lookup, this test fails immediately because byId is empty.
        self::assertSame(
            self::MARKER . 'explicit_handle',
            $sms->exposedResolveSenderIdHandle(),
            'Explicit non-empty handle must short-circuit the priority chain',
        );
    }

    public function testLegacySenderIdIdResolvesViaServiceLookup(): void
    {
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'legacy_sender']);

        $this->stubSenderIds->byId[(int) $sender->id] = $sender;

        $sms = $this->makeSms();
        $sms->senderIdHandle = null;
        $sms->senderIdId = (int) $sender->id;

        self::assertSame(
            self::MARKER . 'legacy_sender',
            $sms->exposedResolveSenderIdHandle(),
            'Pre-3.10 forms with only senderIdId must resolve to the record\'s handle',
        );
    }

    public function testLegacySenderIdIdReturnsNullWhenRecordIsGone(): void
    {
        // Legacy int present but record was deleted in sms-manager — the
        // chain must return null rather than falling back to the default,
        // because the form has explicit (stale) intent toward a specific
        // sender. Falling back would silently misroute the SMS.
        $sms = $this->makeSms();
        $sms->senderIdHandle = null;
        $sms->senderIdId = 9999; // Not seeded into stub.byId — getSenderIdById returns null.

        // Set a default so we can prove the chain does NOT fall through to it.
        $provider = $this->seedProvider();
        $defaultSender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'default_handle']);
        $this->stubSenderIds->defaultSenderId = $defaultSender;

        self::assertNull(
            $sms->exposedResolveSenderIdHandle(),
            'Legacy senderIdId with a missing record must return null, not fall back to default',
        );
    }

    public function testEmptyStringSentinelFallsThroughToDefault(): void
    {
        $provider = $this->seedProvider();
        $defaultSender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'default_handle']);

        $this->stubSenderIds->defaultSenderId = $defaultSender;

        $sms = $this->makeSms();
        $sms->senderIdHandle = ''; // The "Use SMS Manager default" dropdown sentinel.
        $sms->senderIdId = null;

        self::assertSame(
            self::MARKER . 'default_handle',
            $sms->exposedResolveSenderIdHandle(),
            'Empty-string sentinel must resolve to sms-manager\'s current default at dispatch time',
        );
    }

    public function testNullSenderIdHandleAlsoFallsThroughToDefault(): void
    {
        // Yii/Formie serialises a nullable ?string as null whether the admin
        // selected the empty-value sentinel or simply never made a choice.
        // Both states must collapse to "use default".
        $provider = $this->seedProvider();
        $defaultSender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'default_handle']);

        $this->stubSenderIds->defaultSenderId = $defaultSender;

        $sms = $this->makeSms();
        $sms->senderIdHandle = null;
        $sms->senderIdId = null;

        self::assertSame(
            self::MARKER . 'default_handle',
            $sms->exposedResolveSenderIdHandle(),
            'Null senderIdHandle (no choice made) must behave the same as empty-string sentinel',
        );
    }

    public function testNullWhenNothingResolves(): void
    {
        // No handle, no legacy id, no default configured → null. The
        // dispatch caller turns this into an Integration::error()
        // ("No sender ID configured ..."), preserving the sms-manager 8.2
        // fail-loud contract.
        $this->stubSenderIds->defaultSenderId = null;

        $sms = $this->makeSms();
        $sms->senderIdHandle = null;
        $sms->senderIdId = null;

        self::assertNull(
            $sms->exposedResolveSenderIdHandle(),
            'No handle + no legacy id + no default = null (caller surfaces the error)',
        );
    }

    /**
     * Build an Sms integration instance that exposes the protected
     * {@see Sms::resolveSenderIdHandle()} via a public proxy. Anonymous
     * subclass keeps the test from needing reflection — matches the same
     * "extend the real class" pattern sms-manager uses for
     * `BaseProvider::normalizeAndValidatePhone`.
     */
    private function makeSms(): Sms
    {
        return new class extends Sms {
            public function exposedResolveSenderIdHandle(): ?string
            {
                return $this->resolveSenderIdHandle();
            }
        };
    }
}
