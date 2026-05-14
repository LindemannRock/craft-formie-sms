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
use lindemannrock\smsmanager\SmsManager;
use verbb\formie\elements\Form;

/**
 * SMS Manager Integration
 *
 * Reports Formie SMS usage to SMS Manager.
 *
 * @author    LindemannRock
 * @package   FormieSms
 * @since     3.1.0
 */
class SmsManagerIntegration implements IntegrationInterface
{
    /**
     * @inheritdoc
     */
    public function getProviderUsages(int $providerId): array
    {
        $usages = [];

        // Pre-filter at SQL level on the JSON-as-TEXT settings column to
        // avoid hydrating every form. Two patterns covered:
        //
        //   - Legacy (pre-3.10): `"providerId":"<int>"` stored directly on
        //     the form's integration block. Formie quotes integer fields
        //     as strings.
        //   - New (3.10+): the integration no longer stores `providerId` —
        //     only `senderIdHandle`. To find new-format forms that reference
        //     this provider, we collect every sender handle belonging to
        //     this provider and scan for `"senderIdHandle":"<handle>"`.
        //
        // The LIKE matches are coarse (could match `"providerId":"50"` when
        // searching `"providerId":"5"` because LIKE doesn't anchor); the PHP
        // loop below applies an exact match for both patterns.
        $providerSenderHandles = $this->getSenderHandlesForProvider($providerId);

        $likeConditions = [
            ['like', 'settings', '"providerId":"' . (int)$providerId . '"'],
        ];
        foreach ($providerSenderHandles as $handle) {
            $likeConditions[] = ['like', 'settings', '"senderIdHandle":"' . $handle . '"'];
        }

        $matchingIds = (new Query())
            ->select('id')
            ->from('{{%formie_forms}}')
            ->andWhere(array_merge(['or'], $likeConditions))
            ->column();

        if (empty($matchingIds)) {
            return $usages;
        }

        $forms = Form::find()->id($matchingIds)->all();
        $senderHandleSet = array_flip($providerSenderHandles);

        foreach ($forms as $form) {
            $settings = $form->settings?->integrations ?? [];

            foreach ($settings as $handle => $integrationSettings) {
                if (empty($integrationSettings['enabled'])) {
                    continue;
                }

                // Legacy match: provider int stored directly.
                $matchesLegacy = isset($integrationSettings['providerId'])
                    && (int) $integrationSettings['providerId'] === $providerId;

                // New match: a stored senderIdHandle whose sender belongs
                // to this provider. Empty-string sentinel ("Use SMS Manager
                // default") doesn't reference any specific sender — sms-
                // manager has its own delete-guard preventing deletion of
                // the configured default, so we don't need to report it.
                $matchesNew = isset($integrationSettings['senderIdHandle'])
                    && is_string($integrationSettings['senderIdHandle'])
                    && $integrationSettings['senderIdHandle'] !== ''
                    && isset($senderHandleSet[$integrationSettings['senderIdHandle']]);

                if ($matchesLegacy || $matchesNew) {
                    $usages[] = [
                        'label' => $form->title,
                        'editUrl' => UrlHelper::cpUrl('formie/forms/edit/' . $form->id . '#tab-integrations'),
                    ];
                    // One usage per form is enough; don't enumerate every
                    // integration block referencing the same provider.
                    break;
                }
            }
        }

        return $usages;
    }

    /**
     * Return every enabled sender handle belonging to the given provider.
     *
     * Used by `getProviderUsages()` to find new-format forms that reference
     * the provider indirectly via one of its senders' handles.
     *
     * @return string[]
     */
    private function getSenderHandlesForProvider(int $providerId): array
    {
        $provider = SmsManager::$plugin->providers->getProviderById($providerId);
        if (!$provider || !$provider->handle) {
            return [];
        }

        $handles = [];
        foreach (SmsManager::$plugin->senderIds->getSenderIdsByProvider((string) $provider->handle) as $sender) {
            if ($sender->handle) {
                $handles[] = (string) $sender->handle;
            }
        }

        return $handles;
    }

    /**
     * @inheritdoc
     */
    public function getSenderIdUsages(int $senderIdId): array
    {
        $usages = [];

        // Two patterns covered, same rationale as getProviderUsages():
        //
        //   - Legacy (pre-3.10): `"senderIdId":"<int>"`.
        //   - New (3.10+): `"senderIdHandle":"<this sender's handle>"`.
        //
        // The handle is resolved from the int once here; null when the
        // sender record can't be found (shouldn't happen since the delete
        // guard's caller already loaded the record, but defensive).
        $senderHandle = null;
        $senderRecord = SmsManager::$plugin->senderIds->getSenderIdById($senderIdId);
        if ($senderRecord && $senderRecord->handle) {
            $senderHandle = (string) $senderRecord->handle;
        }

        $likeConditions = [
            ['like', 'settings', '"senderIdId":"' . (int)$senderIdId . '"'],
        ];
        if ($senderHandle !== null) {
            $likeConditions[] = ['like', 'settings', '"senderIdHandle":"' . $senderHandle . '"'];
        }

        $matchingIds = (new Query())
            ->select('id')
            ->from('{{%formie_forms}}')
            ->andWhere(array_merge(['or'], $likeConditions))
            ->column();

        if (empty($matchingIds)) {
            return $usages;
        }

        $forms = Form::find()->id($matchingIds)->all();

        foreach ($forms as $form) {
            $settings = $form->settings?->integrations ?? [];

            foreach ($settings as $handle => $integrationSettings) {
                if (empty($integrationSettings['enabled'])) {
                    continue;
                }

                // Legacy match: int FK stored directly on the integration.
                $matchesLegacy = isset($integrationSettings['senderIdId'])
                    && (int) $integrationSettings['senderIdId'] === $senderIdId;

                // New match: stored handle equals this sender's handle.
                // The empty-string sentinel ("Use SMS Manager default")
                // doesn't reference any specific sender — sms-manager's
                // own "can't delete the default sender" guard covers that
                // separately.
                $matchesNew = $senderHandle !== null
                    && isset($integrationSettings['senderIdHandle'])
                    && is_string($integrationSettings['senderIdHandle'])
                    && $integrationSettings['senderIdHandle'] === $senderHandle;

                if ($matchesLegacy || $matchesNew) {
                    $usages[] = [
                        'label' => $form->title,
                        'editUrl' => UrlHelper::cpUrl('formie/forms/edit/' . $form->id . '#tab-integrations'),
                    ];
                    break;
                }
            }
        }

        return $usages;
    }
}
