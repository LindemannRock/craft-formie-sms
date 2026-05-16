<?php
/**
 * LindemannRock Formie SMS
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\tests\Integration;

use lindemannrock\formiesms\integrations\SmsManagerIntegration;
use lindemannrock\formiesms\tests\TestCase;

/**
 * Coverage for {@see SmsManagerIntegration::getProviderUsages()} and
 * {@see SmsManagerIntegration::getSenderIdUsages()}.
 *
 * These two delete-time guards underpin sms-manager's "you can't delete a
 * provider/sender that's still wired up to a Formie form" UX. Audit 2.1
 * burned us once when the LIKE pattern used unquoted ints (`"providerId":1`)
 * but Formie persists form-integer fields as quoted strings
 * (`"providerId":"1"`). The bug let admin delete a sender ID that was still
 * referenced by an active form. The fix quotes the value in the LIKE
 * (`"providerId":"%d"`); this test pins that shape against real seeded
 * forms with both pre-3.10 (`providerId` / `senderIdId`) and 3.10+
 * (`senderIdHandle`) integration blocks.
 *
 * The empty-string sentinel ("Use SMS Manager default") is also pinned as
 * NOT reported — sms-manager's default-sender delete guard handles that
 * case on its own side, so reporting it from here would double up.
 *
 * @since 3.10.0
 */
final class SmsManagerIntegrationUsageScansTest extends TestCase
{
    private SmsManagerIntegration $integration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->integration = new SmsManagerIntegration();
    }

    public function testGetProviderUsagesMatchesBothLegacyAndNewFormatForms(): void
    {
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'sender_alpha']);

        $legacyForm = $this->seedFormieForm([
            'mySms' => [
                'enabled' => true,
                // Pre-3.10 integration block stored providerId directly.
                // Quoted as a string because Formie serialises form-input
                // integer fields that way — audit 2.1 root cause.
                'providerId' => (string) $provider->id,
            ],
        ], 'legacy_form');

        $newFormatForm = $this->seedFormieForm([
            'mySms' => [
                'enabled' => true,
                // 3.10+ integration block stores senderIdHandle instead.
                // The function collects every sender handle belonging to
                // the queried provider and scans for any of them.
                'senderIdHandle' => (string) $sender->handle,
            ],
        ], 'new_form');

        // Unrelated form: enabled, with the integration block present
        // but pointing at a non-existent provider id. Must NOT appear in
        // results — proves the PHP exact-match loop filters LIKE false
        // positives.
        $this->seedFormieForm([
            'mySms' => [
                'enabled' => true,
                'providerId' => '999999',
            ],
        ], 'unrelated_form');

        $usages = $this->integration->getProviderUsages((int) $provider->id);

        $labels = array_column($usages, 'label');
        sort($labels);

        self::assertCount(2, $usages, 'Both legacy and new-format forms must surface');
        self::assertSame(
            [self::MARKER . 'legacy_form', self::MARKER . 'new_form'],
            $labels,
            'Result labels must come from the matching forms\' titles',
        );

        foreach ($usages as $usage) {
            self::assertStringContainsString('formie/forms/edit/', $usage['editUrl']);
            self::assertStringContainsString('#tab-integrations', $usage['editUrl']);
        }
    }

    public function testGetSenderIdUsagesMatchesBothLegacyAndNewFormatForms(): void
    {
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'sender_beta']);

        $legacyForm = $this->seedFormieForm([
            'mySms' => [
                'enabled' => true,
                // Pre-3.10: senderIdId stored as quoted int.
                'senderIdId' => (string) $sender->id,
            ],
        ], 'legacy_sender_form');

        $newFormatForm = $this->seedFormieForm([
            'mySms' => [
                'enabled' => true,
                'senderIdHandle' => (string) $sender->handle,
            ],
        ], 'new_sender_form');

        // Sentinel form: empty-string handle ("Use SMS Manager default").
        // Must NOT surface — sms-manager's default-sender delete guard
        // covers this case separately, so reporting it from here would
        // double up.
        $this->seedFormieForm([
            'mySms' => [
                'enabled' => true,
                'senderIdHandle' => '',
            ],
        ], 'sentinel_form');

        $usages = $this->integration->getSenderIdUsages((int) $sender->id);

        $labels = array_column($usages, 'label');
        sort($labels);

        self::assertCount(2, $usages);
        self::assertSame(
            [self::MARKER . 'legacy_sender_form', self::MARKER . 'new_sender_form'],
            $labels,
            'Empty-string sentinel must be excluded; only specific-handle references count',
        );
    }

    public function testGetProviderUsagesSkipsDisabledIntegrationBlocks(): void
    {
        // The pre-filter still matches a disabled block (LIKE doesn't know
        // about the `enabled` flag), but the PHP loop's `empty(...['enabled'])`
        // guard drops it before adding to $usages. Pin that contract — a
        // disabled integration is effectively "not in use" and the
        // delete-guard should let admin proceed.
        $provider = $this->seedProvider();

        $this->seedFormieForm([
            'mySms' => [
                'enabled' => false,
                'providerId' => (string) $provider->id,
            ],
        ], 'disabled_form');

        $usages = $this->integration->getProviderUsages((int) $provider->id);

        self::assertCount(
            0,
            $usages,
            'Disabled integration blocks must not block deletion',
        );
    }

    public function testGetSenderIdUsagesReturnsEmptyWhenSenderHasNoReferences(): void
    {
        // No forms seeded with this sender's id/handle — empty result is
        // the contract sms-manager's delete-guard relies on for the
        // "free to delete" branch.
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider);

        $usages = $this->integration->getSenderIdUsages((int) $sender->id);

        self::assertSame([], $usages);
    }
}
