<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
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

        // Parse recipients
        $recipientsRaw = $this->renderMessage($this->recipients, $submission);
        $recipients = array_map('trim', explode(',', $recipientsRaw));

        // Parse message
        $message = $this->renderMessage($this->message, $submission);

        // Get the SMS service from SMS Manager
        $smsService = SmsManager::$plugin->sms;

        // Send SMS to each recipient
        foreach ($recipients as $recipient) {
            if (empty($recipient)) {
                continue;
            }

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
        $html = RichTextHelper::getHtmlContent($template, $submission);
        $converter = new HtmlConverter(['strip_tags' => true]);
        return $converter->convert($html);
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
