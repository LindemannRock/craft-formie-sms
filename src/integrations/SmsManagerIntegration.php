<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

namespace lindemannrock\formiesms\integrations;

use craft\helpers\UrlHelper;
use lindemannrock\smsmanager\integrations\IntegrationInterface;
use verbb\formie\elements\Form;
use verbb\formie\Formie;

/**
 * SMS Manager Integration
 *
 * Reports Formie SMS usage to SMS Manager.
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     3.0.0
 */
class SmsManagerIntegration implements IntegrationInterface
{
    /**
     * @inheritdoc
     */
    public function getProviderUsages(int $providerId): array
    {
        $usages = [];

        // Get all forms that have this provider configured
        $forms = Form::find()->all();

        foreach ($forms as $form) {
            $settings = $form->settings->integrations ?? [];

            foreach ($settings as $handle => $integrationSettings) {
                // Check if this is our SMS integration and uses this provider
                if (isset($integrationSettings['providerId']) && (int)$integrationSettings['providerId'] === $providerId) {
                    // Check if integration is enabled
                    if (!empty($integrationSettings['enabled'])) {
                        $usages[] = [
                            'label' => $form->title,
                            'editUrl' => UrlHelper::cpUrl('formie/forms/edit/' . $form->id . '#tab-integrations'),
                        ];
                    }
                }
            }
        }

        return $usages;
    }

    /**
     * @inheritdoc
     */
    public function getSenderIdUsages(int $senderIdId): array
    {
        $usages = [];

        // Get all forms that have this sender ID configured
        $forms = Form::find()->all();

        foreach ($forms as $form) {
            $settings = $form->settings->integrations ?? [];

            foreach ($settings as $handle => $integrationSettings) {
                // Check if this is our SMS integration and uses this sender ID
                if (isset($integrationSettings['senderIdId']) && (int)$integrationSettings['senderIdId'] === $senderIdId) {
                    // Check if integration is enabled
                    if (!empty($integrationSettings['enabled'])) {
                        $usages[] = [
                            'label' => $form->title,
                            'editUrl' => UrlHelper::cpUrl('formie/forms/edit/' . $form->id . '#tab-integrations'),
                        ];
                    }
                }
            }
        }

        return $usages;
    }
}
