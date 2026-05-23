<?php
/**
 * LindemannRock Formie SMS
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\tests\Integration;

use Craft;
use lindemannrock\formiesms\integrations\miscellaneous\Sms;
use lindemannrock\formiesms\tests\Stubs\StubSenderIdsService;
use lindemannrock\formiesms\tests\Stubs\StubSmsService;
use lindemannrock\formiesms\tests\TestCase;
use verbb\formie\elements\Submission;

/**
 * Coverage for {@see Sms::sendPayload()}'s post-render recipient parsing.
 *
 * 3.9.0 introduced a recipient-parsing change that fragmented Formie's
 * rich-text JSON when admin's recipients template had embedded commas
 * (audit 6.1, HIGH). 3.10.0 reverted to render-first parsing, kept the
 * per-token shape check 3.9.0 added (audit 1.1, the comma-injection
 * hardening intent), and added libphonenumber-backed normalisation so
 * `{field:phone}`'s INTERNATIONAL formatting collapses to E.164 before
 * the strict regex (audit 6.2). All three contracts converge on one
 * loop, pinned here against a stub SmsService that records the dispatched
 * tuples.
 *
 * The integration's dependencies are stubbed three ways:
 *  - `senderIds` swapped → priority chain returns a known handle every time
 *  - `sms` swapped → dispatch is observable + cannot leak outbound SMS
 *  - `renderMessage()` overridden in an anonymous subclass → tests feed
 *    pre-rendered strings directly so the assertions hinge on the
 *    parse/normalise/filter loop, not Formie's RichTextHelper rendering
 *
 * @since 3.10.0
 */
final class SmsRecipientParsingTest extends TestCase
{
    private StubSmsService $stubSms;

    protected function setUp(): void
    {
        parent::setUp();

        $provider = $this->seedProvider();
        $defaultSender = $this->seedSenderId($provider, ['handle' => self::MARKER . 'default_handle']);

        $stubSenderIds = new StubSenderIdsService();
        $stubSenderIds->defaultSenderId = $defaultSender;
        $this->swapPluginComponent('sms-manager', 'senderIds', $stubSenderIds);

        $this->stubSms = new StubSmsService();
        $this->swapPluginComponent('sms-manager', 'sms', $this->stubSms);
    }

    public function testDispatchesEachCommaSeparatedRecipient(): void
    {
        // Plain-text comma-separated recipient list — the canonical
        // multi-recipient case the admin types in. Both should reach
        // the dispatch with their values intact.
        $sms = $this->makeSms('+96597255330, +96560632020');
        $sms->message = 'msg-template';
        $sms->senderIdHandle = ''; // Falls through to seeded default.

        $result = $sms->sendPayload($this->makeSubmission());

        self::assertTrue($result, 'sendPayload() should return true on the happy path');
        self::assertCount(2, $this->stubSms->sentCalls);
        self::assertSame('+96597255330', $this->stubSms->sentCalls[0]['to']);
        self::assertSame('+96560632020', $this->stubSms->sentCalls[1]['to']);
        self::assertSame(self::MARKER . 'default_handle', $this->stubSms->sentCalls[0]['senderIdHandle']);
        self::assertSame('formie-sms', $this->stubSms->sentCalls[0]['sourcePlugin']);
    }

    public function testCollapsesInternationalSpacingToE164(): void
    {
        // Audit 6.2: `{field:phone}` (the PhoneModel object) renders as
        // PhoneNumberFormat::INTERNATIONAL when the field has country-code
        // enabled — `+965 9725 5330` with thin spaces. The strict regex
        // would reject that shape on its own; `toPhoneString()` re-parses
        // via libphonenumber and emits clean E.164 (`+96597255330`).
        // No-op for already-clean inputs.
        $sms = $this->makeSms('+965 9725 5330, +96560632020');
        $sms->message = 'msg-template';
        $sms->senderIdHandle = '';

        $result = $sms->sendPayload($this->makeSubmission());

        self::assertTrue($result);
        self::assertCount(2, $this->stubSms->sentCalls);
        self::assertSame(
            '+96597255330',
            $this->stubSms->sentCalls[0]['to'],
            'INTERNATIONAL format with spaces must collapse to E.164 before dispatch (audit 6.2)',
        );
        self::assertSame('+96560632020', $this->stubSms->sentCalls[1]['to']);
    }

    public function testRejectsCommaInjectedNonPhoneTokens(): void
    {
        // Audit 1.1: admin's recipients template may reference a form
        // field (`"{phoneField}"`, etc.). A submitter who controls that
        // field can inject `,ATTACKER` and try to fan the SMS out. The
        // per-token `^\+?[0-9]{6,15}$` check rejects anything that isn't
        // a clean phone, so the injected payload reaches the warning log
        // path instead of the dispatch path. Pinning this proves the
        // 3.9.0 hardening intent survived the 3.10.0 render-first revert.
        $sms = $this->makeSms('+96560632020, ATTACKER_INJECTED_TEXT, +96597255330');
        $sms->message = 'msg-template';
        $sms->senderIdHandle = '';

        $result = $sms->sendPayload($this->makeSubmission());

        self::assertTrue($result);
        self::assertCount(
            2,
            $this->stubSms->sentCalls,
            'Only the 2 valid phone numbers should be dispatched; injected text must be filtered',
        );
        self::assertSame('+96560632020', $this->stubSms->sentCalls[0]['to']);
        self::assertSame('+96597255330', $this->stubSms->sentCalls[1]['to']);
    }

    public function testReturnsFalseWhenNoValidRecipientsAfterRender(): void
    {
        // Every rendered token fails the regex — the integration must
        // surface this as an explicit failure (Integration::error +
        // return false), NOT silently no-op. Audit 2.3 fix: prevents
        // a misconfigured `recipients` template from looking like a
        // successful send in audit logs.
        $sms = $this->makeSms(',ONLY_JUNK, ANOTHER_JUNK,');
        $sms->message = 'msg-template';
        $sms->senderIdHandle = '';

        $result = $sms->sendPayload($this->makeSubmission());

        self::assertFalse($result, 'No valid recipients = explicit failure, not silent no-op');
        self::assertCount(0, $this->stubSms->sentCalls);
    }

    /**
     * Build an Sms integration that pre-renders both `recipients` and
     * `message` to controlled strings. Anonymous subclass overrides the
     * (protected) `renderMessage()` to dispatch on which template is
     * being rendered — Formie's `RichTextHelper` is never invoked.
     *
     * The `recipients` rendered output is set explicitly via the
     * constructor argument; `message` always renders to the literal
     * `msg-output` so dispatch payload assertions stay simple.
     */
    private function makeSms(string $rendered): Sms
    {
        $sms = new class ($rendered) extends Sms {
            public function __construct(public string $renderedRecipients)
            {
                parent::__construct();
            }

            protected function renderMessage(string $template, Submission $submission): string
            {
                // The integration calls renderMessage twice per send —
                // once for `recipients`, once for `message`. Branch on
                // the property values so we don't have to dispatch on
                // the template string itself.
                if ($template === (string) $this->recipients) {
                    return $this->renderedRecipients;
                }
                return 'msg-output';
            }
        };
        $sms->recipients = '__recipients_template__';

        return $sms;
    }

    /**
     * Build a minimal Submission instance suitable for `sendPayload()`'s
     * pre-dispatch reads: `getSite()->getLocale()->getLanguageID()` and
     * `->id` (forwarded as the `sourceElementId` to sms-manager). We
     * never save it — it's a transient model carrying just the siteId
     * Submission needs to resolve a Site, plus a dummy id for the log
     * row attribution path.
     */
    private function makeSubmission(): Submission
    {
        $submission = new Submission();
        $submission->siteId = Craft::$app->getSites()->getPrimarySite()->id;
        $submission->id = 999999; // arbitrary — log row write is stubbed out

        return $submission;
    }
}
