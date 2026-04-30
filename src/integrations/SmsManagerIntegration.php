<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\integrations;

use craft\db\Query;
use craft\helpers\UrlHelper;
use lindemannrock\smsmanager\integrations\IntegrationInterface;
use verbb\formie\elements\Form;

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

        // Pre-filter at SQL level on the JSON-as-TEXT settings column to avoid hydrating every form.
        // Formie stores form-input integer fields as quoted strings ("providerId":"1"), so the LIKE
        // pattern includes the quotes. The LIKE is still coarse (could match "providerId":"50" when
        // searching for "5"); the PHP loop below does the exact match.
        $matchingIds = (new Query())
            ->select('id')
            ->from('{{%formie_forms}}')
            ->andWhere(['like', 'settings', '"providerId":"' . (int)$providerId . '"'])
            ->column();

        if (empty($matchingIds)) {
            return $usages;
        }

        $forms = Form::find()->id($matchingIds)->all();

        foreach ($forms as $form) {
            $settings = $form->settings?->integrations ?? [];

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

        // Pre-filter at SQL level on the JSON-as-TEXT settings column. See getProviderUsages() above
        // for the rationale (Formie stores integer fields as quoted strings). PHP loop below does
        // the exact match.
        $matchingIds = (new Query())
            ->select('id')
            ->from('{{%formie_forms}}')
            ->andWhere(['like', 'settings', '"senderIdId":"' . (int)$senderIdId . '"'])
            ->column();

        if (empty($matchingIds)) {
            return $usages;
        }

        $forms = Form::find()->id($matchingIds)->all();

        foreach ($forms as $form) {
            $settings = $form->settings?->integrations ?? [];

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
