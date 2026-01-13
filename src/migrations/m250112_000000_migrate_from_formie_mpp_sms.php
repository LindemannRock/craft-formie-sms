<?php
/**
 * Formie SMS plugin for Craft CMS 5.x
 *
 * Migration from formie-mpp-sms to formie-sms
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\helpers\Json;

/**
 * Migrate from formie-mpp-sms to formie-sms
 *
 * This migration:
 * 1. Creates MPP-SMS provider in sms-manager (if not exists)
 * 2. Creates sender IDs in sms-manager from config mapping
 * 3. Updates formie_integrations type and settings
 * 4. Updates formie_forms settings to use new sender ID IDs
 */
class m250112_000000_migrate_from_formie_mpp_sms extends Migration
{
    private const OLD_INTEGRATION_TYPE = 'BuildForHumans\\FormieMppSms\\MppSmsIntegration';
    private const NEW_INTEGRATION_TYPE = 'lindemannrock\\formiesms\\integrations\\miscellaneous\\Sms';

    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Check if sms-manager tables exist
        if (!$this->db->tableExists('{{%smsmanager_providers}}')) {
            Craft::warning('SMS Manager tables not found. Skipping migration.', __METHOD__);
            return true;
        }

        // Check if there are any old integrations to migrate
        $oldIntegrations = (new Query())
            ->from('{{%formie_integrations}}')
            ->where(['type' => self::OLD_INTEGRATION_TYPE])
            ->all();

        if (empty($oldIntegrations)) {
            Craft::info('No formie-mpp-sms integrations found. Skipping migration.', __METHOD__);
            return true;
        }

        // Step 1: Create or get MPP-SMS provider
        $providerId = $this->ensureMppSmsProvider();

        // Step 2: Get sender ID mapping from config
        $senderIdMapping = $this->getSenderIdMapping($providerId);

        // Step 3: Update formie_integrations
        $this->updateIntegrations($oldIntegrations, $providerId);

        // Step 4: Update formie_forms settings
        $this->updateFormSettings($oldIntegrations, $senderIdMapping, $providerId);

        Craft::info('Successfully migrated from formie-mpp-sms to formie-sms.', __METHOD__);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        // This migration is not reversible
        Craft::warning('This migration cannot be reverted.', __METHOD__);
        return false;
    }

    /**
     * Ensure MPP-SMS provider exists in sms-manager
     */
    private function ensureMppSmsProvider(): int
    {
        // Check if provider already exists
        $provider = (new Query())
            ->from('{{%smsmanager_providers}}')
            ->where(['handle' => 'mpp-sms'])
            ->one();

        if ($provider) {
            return (int) $provider['id'];
        }

        // Create the provider
        $this->insert('{{%smsmanager_providers}}', [
            'name' => 'MPP-SMS',
            'handle' => 'mpp-sms',
            'type' => 'mpp-sms',
            'settings' => Json::encode([
                'apiKey' => '$MPP_SMS_API_KEY',
            ]),
            'enabled' => true,
            'isDefault' => true,
            'sortOrder' => 1,
            'dateCreated' => date('Y-m-d H:i:s'),
            'dateUpdated' => date('Y-m-d H:i:s'),
            'uid' => \craft\helpers\StringHelper::UUID(),
        ]);

        return (int) $this->db->getLastInsertID('{{%smsmanager_providers}}');
    }

    /**
     * Get sender ID mapping from old config to new database IDs
     */
    private function getSenderIdMapping(int $providerId): array
    {
        $mapping = [];

        // Try to read the old config file
        $configPath = Craft::$app->getPath()->getConfigPath() . '/formie-mpp-sms.php';
        $oldConfig = [];

        if (file_exists($configPath)) {
            $oldConfig = require $configPath;
        }

        $senderIdOptions = $oldConfig['senderIdOptions'] ?? [];

        foreach ($senderIdOptions as $handle => $name) {
            // Check if sender ID already exists
            $existing = (new Query())
                ->from('{{%smsmanager_senderids}}')
                ->where([
                    'providerId' => $providerId,
                    'handle' => $handle,
                ])
                ->one();

            if ($existing) {
                $mapping[$handle] = (int) $existing['id'];
                continue;
            }

            // Create the sender ID
            $this->insert('{{%smsmanager_senderids}}', [
                'providerId' => $providerId,
                'name' => $name,
                'handle' => $handle,
                'senderId' => $name,
                'enabled' => true,
                'isDefault' => count($mapping) === 0, // First one is default
                'isTest' => false,
                'sortOrder' => count($mapping) + 1,
                'dateCreated' => date('Y-m-d H:i:s'),
                'dateUpdated' => date('Y-m-d H:i:s'),
                'uid' => \craft\helpers\StringHelper::UUID(),
            ]);

            $mapping[$handle] = (int) $this->db->getLastInsertID('{{%smsmanager_senderids}}');
        }

        return $mapping;
    }

    /**
     * Update formie_integrations table
     */
    private function updateIntegrations(array $integrations, int $providerId): void
    {
        foreach ($integrations as $integration) {
            $oldSettings = Json::decode($integration['settings']) ?? [];

            // New settings format
            $newSettings = [
                'providerId' => $providerId,
                'senderIdId' => null, // Will be set per-form
                'recipients' => $oldSettings['recipients'] ?? null,
                'message' => $oldSettings['message'] ?? null,
                'language' => $oldSettings['language'] ?? 'any',
            ];

            $this->update(
                '{{%formie_integrations}}',
                [
                    'type' => self::NEW_INTEGRATION_TYPE,
                    'settings' => Json::encode($newSettings),
                ],
                ['id' => $integration['id']]
            );

            Craft::info("Updated integration: {$integration['handle']}", __METHOD__);
        }
    }

    /**
     * Update formie_forms settings
     */
    private function updateFormSettings(array $integrations, array $senderIdMapping, int $providerId): void
    {
        // Get integration handles
        $integrationHandles = array_column($integrations, 'handle');

        // Get all forms that might have these integrations
        $forms = (new Query())
            ->from('{{%formie_forms}}')
            ->all();

        foreach ($forms as $form) {
            $settings = Json::decode($form['settings']) ?? [];
            $formIntegrations = $settings['integrations'] ?? [];
            $updated = false;

            foreach ($integrationHandles as $handle) {
                if (!isset($formIntegrations[$handle])) {
                    continue;
                }

                $formIntegration = $formIntegrations[$handle];

                // Map old senderId string to new senderIdId int
                $oldSenderId = $formIntegration['senderId'] ?? null;
                $newSenderIdId = null;

                if ($oldSenderId && isset($senderIdMapping[$oldSenderId])) {
                    $newSenderIdId = $senderIdMapping[$oldSenderId];
                }

                // Update the integration settings
                $formIntegrations[$handle] = [
                    'enabled' => $formIntegration['enabled'] ?? '',
                    'providerId' => $providerId,
                    'senderIdId' => $newSenderIdId,
                    'recipients' => $formIntegration['recipients'] ?? null,
                    'message' => $formIntegration['message'] ?? null,
                    'language' => $formIntegration['language'] ?? 'any',
                ];

                $updated = true;
            }

            if ($updated) {
                $settings['integrations'] = $formIntegrations;

                $this->update(
                    '{{%formie_forms}}',
                    ['settings' => Json::encode($settings)],
                    ['id' => $form['id']]
                );

                Craft::info("Updated form: {$form['handle']}", __METHOD__);
            }
        }
    }
}
