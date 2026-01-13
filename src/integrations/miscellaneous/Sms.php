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
use lindemannrock\smsmanager\SmsManager;
use verbb\formie\base\Integration;
use verbb\formie\base\Miscellaneous;
use verbb\formie\elements\Submission;
use verbb\formie\helpers\RichTextHelper;

/**
 * SMS Integration for Formie
 *
 * Sends SMS notifications via SMS Manager on form submission.
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     1.0.0
 */
class Sms extends Miscellaneous
{
    /**
     * @var int|null The provider ID from SMS Manager
     */
    public ?int $providerId = null;

    /**
     * @var int|null The sender ID from SMS Manager
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
        // Use sms-manager plugin name if available
        if (Craft::$app->plugins->isPluginInstalled('sms-manager') && class_exists(SmsManager::class)) {
            $plugin = SmsManager::$plugin;
            if ($plugin !== null) {
                return $plugin->getSettings()->getFullName();
            }
        }

        return Craft::t('formie-sms', 'SMS Manager');
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
        Craft::info('sendPayload called for submission: ' . $submission->id, __METHOD__);

        // Check if SMS Manager is installed
        if (!$this->isSmsManagerInstalled()) {
            Integration::error($this, Craft::t('formie-sms', 'SMS Manager plugin is not installed.'));
            Craft::error('SMS Manager plugin is not installed', __METHOD__);
            return false;
        }

        Craft::info('SMS Manager is installed', __METHOD__);

        // Get the submission's site language
        $originLanguage = $submission->getSite()->getLocale()->getLanguageID();

        Craft::info("Language check - Origin: '{$originLanguage}', Filter: '{$this->language}'", __METHOD__);

        // Check language filter
        if ($this->language !== 'any' && $originLanguage !== $this->language) {
            Craft::warning(
                "Skipping SMS as form submission was from a different language: '{$originLanguage}' (expected: '{$this->language}')",
                __METHOD__
            );
            return true; // Return true as this is expected behavior, not an error
        }

        Craft::info('Language check passed', __METHOD__);

        // Parse recipients
        $recipientsRaw = $this->renderMessage($this->recipients, $submission);
        $recipients = array_map('trim', explode(',', $recipientsRaw));

        Craft::info('Parsed recipients: ' . json_encode($recipients), __METHOD__);

        // Parse message
        $message = $this->renderMessage($this->message, $submission);

        Craft::info('Parsed message: ' . substr($message, 0, 100), __METHOD__);

        // Get the SMS service from SMS Manager
        $smsService = SmsManager::$plugin->sms;

        // Send SMS to each recipient
        foreach ($recipients as $recipient) {
            if (empty($recipient)) {
                Craft::info('Skipping empty recipient', __METHOD__);
                continue;
            }

            Craft::info("Attempting to send SMS to: {$recipient}", __METHOD__);

            try {
                $result = $smsService->send(
                    $recipient,
                    $message,
                    $originLanguage,
                    $this->providerId,
                    $this->senderIdId,
                    'formie-sms',
                    $submission->id
                );

                Craft::info("SMS send result for {$recipient}: " . ($result ? 'success' : 'failed'), __METHOD__);

                if (!$result) {
                    Integration::error($this, Craft::t('formie-sms', 'Failed to send SMS to {recipient}', [
                        'recipient' => $recipient,
                    ]));
                }
            } catch (\Throwable $e) {
                Craft::error("SMS send exception: " . $e->getMessage(), __METHOD__);
                $exception = $e instanceof \Exception ? $e : new \Exception($e->getMessage(), (int) $e->getCode(), $e);
                Integration::apiError($this, $exception);
            }
        }

        Craft::info('sendPayload completed', __METHOD__);

        return true;
    }

    /**
     * Check if SMS Manager plugin is installed and enabled
     */
    private function isSmsManagerInstalled(): bool
    {
        return Craft::$app->plugins->isPluginInstalled('sms-manager')
            && Craft::$app->plugins->isPluginEnabled('sms-manager');
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
        Craft::info('parsePhoneFieldVariables input: ' . substr($template, 0, 500), __METHOD__);

        // Match phone field variable patterns in the rich text JSON
        // Pattern: "value":"{field.HANDLE.PROPERTY}" or "{field:HANDLE.PROPERTY}"
        // Different Formie versions use different separators (dot vs colon)
        return preg_replace_callback(
            '/\{field[.:]([^.}]+)\.(countryCode|number|country|countryName)\}/',
            function($matches) use ($submission) {
                $fieldHandle = $matches[1];
                $property = $matches[2];

                Craft::info("Phone field match: handle={$fieldHandle}, property={$property}", __METHOD__);

                // Get the field value from submission
                $value = $submission->getFieldValue($fieldHandle);

                Craft::info("Phone field value type: " . gettype($value) . ", value: " . json_encode($value), __METHOD__);

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
