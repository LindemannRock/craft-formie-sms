<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\integrations\miscellaneous;

use Craft;
use craft\helpers\UrlHelper;
use League\HTMLToMarkdown\HtmlConverter;
use lindemannrock\base\helpers\PluginHelper;
use lindemannrock\smsmanager\SmsManager;
use verbb\formie\base\Integration;
use verbb\formie\base\Miscellaneous;
use verbb\formie\elements\Submission;
use verbb\formie\helpers\RichTextHelper;
use verbb\formie\models\Phone as FormiePhone;

/**
 * SMS Integration for Formie
 *
 * Sends SMS notifications via SMS Manager on form submission.
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     3.0.0
 */
class Sms extends Miscellaneous
{
    /**
     * @var string|null The sender ID handle from SMS Manager.
     *
     * Empty string (`''`) is the "Use SMS Manager default" sentinel — the
     * actual sender is resolved at dispatch time via
     * `SmsManager::$plugin->senderIds->getDefaultSenderId()`. `null` means
     * no handle has been saved yet (typically a legacy form saved before
     * 3.10.0 — `resolveSenderIdHandle()` falls back to the deprecated
     * `senderIdId` field in that case).
     *
     * @since 3.10.0
     */
    public ?string $senderIdHandle = null;

    /**
     * @var int|null Legacy provider ID.
     *
     * Ignored at dispatch — the provider is derived from the sender's
     * `providerHandle` inside SMS Manager's `sendWithHandle()`. Kept for
     * back-compat reads on forms saved before 3.10.0. Will be removed in
     * a future release once both prod test installs have migrated via the
     * `formie-sms/migrate/integration-handles` console command.
     *
     * @deprecated 3.10.0
     */
    public ?int $providerId = null;

    /**
     * @var int|null Legacy sender ID.
     *
     * Used as a fallback by `resolveSenderIdHandle()` when
     * `senderIdHandle` is null (pre-3.10.0 forms). Will be removed once
     * all installs have migrated.
     *
     * @deprecated 3.10.0
     */
    public ?int $senderIdId = null;

    /**
     * @var string|null The recipient phone number(s)
     */
    public ?string $recipients = null;

    /**
     * @var string|null The message content
     */
    public ?string $message = null;

    /**
     * @var string The language filter (site language code or 'any')
     */
    public string $language = 'any';

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return PluginHelper::getPluginName('sms-manager', Craft::t('formie-sms', 'SMS Manager'));
    }

    /**
     * @inheritdoc
     */
    public static function supportsConnection(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return Craft::t('formie-sms', 'Send SMS notifications via SMS Manager on form submission.');
    }

    /**
     * @inheritdoc
     */
    public function getIconUrl(): string
    {
        return Craft::$app->getAssetManager()->getPublishedUrl('@lindemannrock/formiesms/web/assets/sms-icon.svg', true) ?: '';
    }

    /**
     * @inheritdoc
     */
    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('formie/settings/miscellaneous/edit/' . $this->id);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('formie-sms/integrations/miscellaneous/sms/_plugin-settings', [
            'integration' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getFormSettingsHtml($form): string
    {
        // Get providers and sender IDs from SMS Manager
        $providers = $this->getProviderOptions();
        $senderIds = $this->getSenderIdOptions();
        $languages = $this->getLanguageOptions();

        return Craft::$app->getView()->renderTemplate('formie-sms/integrations/miscellaneous/sms/_form-settings', [
            'integration' => $this,
            'form' => $form,
            'providers' => $providers,
            'senderIds' => $senderIds,
            'languages' => $languages,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        // Validate the following when saving form settings
        $rules[] = [
            [
                'providerId',
                'senderIdId',
                'recipients',
                'message',
            ],
            'required',
            'on' => [Integration::SCENARIO_FORM],
            'when' => function($integration) {
                /** @var Integration $integration */
                return $integration->getEnabled();
            },
        ];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function sendPayload(Submission $submission): bool
    {
        // Check if SMS Manager is installed
        if (!$this->isSmsManagerInstalled()) {
            Integration::error($this, Craft::t('formie-sms', 'SMS Manager plugin is not installed.'));
            return false;
        }

        // Get the submission's site language
        $originLanguage = $submission->getSite()->getLocale()->getLanguageID();

        // Check language filter
        if ($this->language !== 'any' && $originLanguage !== $this->language) {
            Craft::warning(
                "Skipping SMS as form submission was from a different language: '{$originLanguage}' (expected: '{$this->language}')",
                __METHOD__
            );
            return true; // Return true as this is expected behavior, not an error
        }

        // Render the full recipients template first (RichTextHelper handles
        // Formie's rich-text JSON storage + variable tag substitution),
        // then split the rendered plain text on commas. Splitting the raw
        // JSON before rendering — which 3.9.0 briefly did for security —
        // fragments the JSON structure and breaks every code path that
        // expects intact rich-text JSON downstream. The per-token phone
        // regex below catches any tokens that don't look like E.164
        // phones, which preserves the security intent of the 3.9.0
        // change (a submitter-controlled variable that smuggles a comma
        // gets split into pieces that each fail the strict phone regex).
        $recipientsRaw = trim($this->renderMessage((string)$this->recipients, $submission));

        $recipients = [];
        foreach (explode(',', $recipientsRaw) as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }
            // Normalize via Formie's own libphonenumber-backed helper:
            // `{field:phone}` (the PhoneModel object) renders as INTERNATIONAL
            // format with spaces (`+965 6063 2020`) when the field has
            // country-code enabled, which our strict E.164 regex rejects.
            // `toPhoneString()` re-parses and re-formats as E.164 (no spaces).
            // No-op for already-clean inputs like `97255330` or `+96597255330`.
            $token = FormiePhone::toPhoneString($token);
            if (!preg_match('/^\+?[0-9]{6,15}$/', $token)) {
                Craft::warning("Skipping invalid SMS recipient '{$token}' (rendered recipients: '{$recipientsRaw}')", __METHOD__);
                continue;
            }
            $recipients[] = $token;
        }

        if ($recipients === []) {
            Integration::error($this, Craft::t('formie-sms', 'No valid recipients after rendering — SMS not sent. Check the integration\'s "Recipients" template and the submission data.'));
            return false;
        }

        // Resolve which sender ID handle this integration should use. Handle
        // empty string ('') resolves to SMS Manager's current default at
        // dispatch time; null falls through the legacy senderIdId compat
        // shim for pre-3.10 forms.
        $senderIdHandle = $this->resolveSenderIdHandle();

        if ($senderIdHandle === null) {
            Integration::error($this, Craft::t('formie-sms', 'No sender ID configured for this integration. Edit the integration settings and pick a sender, or pick "Use SMS Manager default" after configuring one in SMS Manager.'));
            return false;
        }

        // Parse message
        $message = $this->renderMessage($this->message, $submission);

        // Route through SMS Manager's handle-based send so config-only
        // senders work the same as DB-backed ones, and so a misconfigured
        // SMS Manager default surfaces as an explicit failure rather than
        // a silent substitution (sms-manager audit 8.2).
        $smsService = SmsManager::$plugin->sms;

        foreach ($recipients as $recipient) {
            try {
                $result = $smsService->sendWithHandle(
                    $recipient,
                    $message,
                    $senderIdHandle,
                    $originLanguage,
                    'formie-sms',
                    $submission->id,
                );

                if (!$result) {
                    Integration::error($this, Craft::t('formie-sms', 'Failed to send SMS to {recipient}', [
                        'recipient' => $recipient,
                    ]));
                }
            } catch (\Throwable $e) {
                $exception = $e instanceof \Exception ? $e : new \Exception($e->getMessage(), (int) $e->getCode(), $e);
                Integration::apiError($this, $exception);
            }
        }

        return true;
    }

    /**
     * Resolve the sender ID handle this integration should send under.
     *
     * Priority chain:
     *  1. `$senderIdHandle === ''` → "Use SMS Manager default" sentinel,
     *     resolve `SmsManager`'s current default at dispatch time. Returns
     *     the default's handle on success, or `null` if no default is
     *     configured / the configured default doesn't resolve (sms-manager
     *     8.2 fail-loud behaviour — caller surfaces an error).
     *  2. `$senderIdHandle` set to a non-empty string → use it directly.
     *  3. Legacy compat: `$senderIdId` set (pre-3.10 forms) → look up the
     *     record and use its handle.
     *  4. Nothing set anywhere → `null`.
     *
     * @return string|null The handle to dispatch under, or null on failure.
     */
    private function resolveSenderIdHandle(): ?string
    {
        // Priority 1: empty-string sentinel → SMS Manager's current default.
        if ($this->senderIdHandle === '') {
            $default = SmsManager::$plugin->senderIds->getDefaultSenderId();
            return $default?->handle;
        }

        // Priority 2: explicit handle stored on this integration.
        if ($this->senderIdHandle !== null) {
            return $this->senderIdHandle;
        }

        // Priority 3: legacy int field on pre-3.10 forms — resolve to handle.
        if ($this->senderIdId !== null) {
            $record = SmsManager::$plugin->senderIds->getSenderIdById($this->senderIdId);
            return $record?->handle;
        }

        return null;
    }

    /**
     * Check if SMS Manager plugin is installed and enabled
     */
    private function isSmsManagerInstalled(): bool
    {
        return PluginHelper::isPluginInstalled('sms-manager')
            && PluginHelper::isPluginEnabled('sms-manager');
    }

    /**
     * Render a message template with submission data
     */
    private function renderMessage(string $template, Submission $submission): string
    {
        // First, handle phone field subproperties that RichTextHelper doesn't support
        // Phone fields don't extend SubField, so {field:phone.countryCode} etc. don't work
        $template = $this->parsePhoneFieldVariables($template, $submission);

        $html = RichTextHelper::getHtmlContent($template, $submission);
        $converter = new HtmlConverter(['strip_tags' => true]);

        return $converter->convert($html);
    }

    /**
     * Parse phone field variables that RichTextHelper doesn't handle
     * Converts {field:phone.countryCode}, {field:phone.number} etc. to actual values
     */
    private function parsePhoneFieldVariables(string $template, Submission $submission): string
    {
        // Match phone field variable patterns in the rich text JSON
        // Pattern: "value":"{field.HANDLE.PROPERTY}" or "{field:HANDLE.PROPERTY}"
        // Different Formie versions use different separators (dot vs colon)
        return preg_replace_callback(
            '/\{field[.:]([^.}]+)\.(countryCode|number|country|countryName)\}/',
            function($matches) use ($submission) {
                $fieldHandle = $matches[1];
                $property = $matches[2];

                // Get the field value from submission
                $value = $submission->getFieldValue($fieldHandle);

                if ($value === null) {
                    Craft::warning("Phone field '{$fieldHandle}' not found in submission", __METHOD__);
                    return '';
                }

                // PhoneModel has these properties
                if (is_object($value)) {
                    return match ($property) {
                        'countryCode' => $value->countryCode ?? '',
                        'number' => $value->number ?? '',
                        'country' => $value->country ?? '',
                        'countryName' => $value->countryName ?? '',
                    };
                }

                return '';
            },
            $template
        );
    }

    /**
     * Get provider options from SMS Manager
     */
    private function getProviderOptions(): array
    {
        $options = [];

        if (!$this->isSmsManagerInstalled()) {
            return $options;
        }

        $providers = SmsManager::$plugin->providers->getAllProviders();

        foreach ($providers as $provider) {
            if ($provider->enabled) {
                $options[] = [
                    'value' => $provider->id,
                    'label' => $provider->name,
                ];
            }
        }

        return $options;
    }

    /**
     * Get sender ID options from SMS Manager
     */
    private function getSenderIdOptions(): array
    {
        $options = [];

        if (!$this->isSmsManagerInstalled()) {
            return $options;
        }

        $senderIds = SmsManager::$plugin->senderIds->getAllSenderIds();

        foreach ($senderIds as $senderId) {
            if ($senderId->enabled) {
                $options[] = [
                    'value' => $senderId->id,
                    'label' => $senderId->name,
                    'providerId' => $senderId->providerId,
                ];
            }
        }

        return $options;
    }

    /**
     * Get language options from Craft sites
     */
    private function getLanguageOptions(): array
    {
        $options = [
            [
                'value' => 'any',
                'label' => Craft::t('formie-sms', 'Any Language'),
            ],
        ];

        $seenLanguages = [];
        foreach (Craft::$app->getSites()->getAllSites() as $site) {
            $langCode = explode('-', $site->language)[0];
            if (!in_array($langCode, $seenLanguages, true)) {
                $seenLanguages[] = $langCode;
                $options[] = [
                    'value' => $langCode,
                    'label' => $site->language . ' (' . $site->name . ')',
                ];
            }
        }

        return $options;
    }
}
