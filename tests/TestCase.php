<?php
/**
 * LindemannRock Formie SMS
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2026 LindemannRock
 */

declare(strict_types=1);

namespace lindemannrock\formiesms\tests;

use Craft;
use lindemannrock\base\testing\IntegrationTestCase;
use lindemannrock\smsmanager\records\ProviderRecord;
use lindemannrock\smsmanager\records\SenderIdRecord;
use lindemannrock\smsmanager\services\ProvidersService;
use lindemannrock\smsmanager\services\SenderIdsService;
use lindemannrock\smsmanager\SmsManager;
use verbb\formie\elements\Form as FormieForm;

/**
 * Base test case for formie-sms integration tests.
 *
 * Layers plugin-specific shorthand on top of {@see IntegrationTestCase}:
 *  - direct accessors for sms-manager's `providers` / `senderIds` services
 *    (the integration's resolveSenderIdHandle + the usage scans both read
 *    sms-manager state, so every test needs these handy)
 *  - marker-prefixed seeders for sms-manager provider + sender ID records
 *    (mirrors the sms-manager rollout's seedProvider/seedSenderId shape so
 *    cross-plugin tests stay legible)
 *  - `__formiesms_test_` marker rides on the sms-manager `providers.handle`
 *    and `sender_ids.handle` columns, plus `formie_forms.title` for the
 *    usage-scan tests that seed real Formie Form elements
 *
 * The integration class never writes its own DB rows — every persistence
 * site lives in sms-manager or Formie. Cleanup pivots on those two
 * sub-systems' tables.
 *
 * @since 3.10.0
 */
abstract class TestCase extends IntegrationTestCase
{
    /**
     * Marker prefix used for every test-seeded row.
     *
     * Applied to `handle` columns on the sms-manager providers + sender_ids
     * tables, and to `title` on the formie_forms table. Plain ASCII so
     * `purgeRowsByMarker`'s LIKE wildcard isn't tripped by any regex
     * meta-characters in the prefix itself.
     */
    protected const MARKER = '__formiesms_test_';

    protected ProvidersService $providers;

    protected SenderIdsService $senderIds;

    protected function setUp(): void
    {
        parent::setUp();
        $plugin = SmsManager::$plugin;
        $this->providers = $plugin->providers;
        $this->senderIds = $plugin->senderIds;
        $this->purgeTestRows();
    }

    protected function tearDown(): void
    {
        $this->purgeTestRows();
        parent::tearDown();
    }

    /**
     * Seed a saved sms-manager {@see ProviderRecord} with the marker prefix
     * on its `handle` column so it's drained on tearDown.
     *
     * The record's `type` defaults to a marker-prefixed value rather than a
     * real provider type — the tests in this plugin don't exercise dispatch
     * (the `SmsService::sendWithHandle()` step is stubbed out), so the type
     * never has to resolve to a registered provider class. That keeps this
     * harness from having to register a stub provider type the way
     * sms-manager's own suite does.
     *
     * @param array<string, mixed> $overrides
     */
    protected function seedProvider(array $overrides = []): ProviderRecord
    {
        $handle = $overrides['handle'] ?? $this->nextTestMarker(self::MARKER, 'provider');

        $record = new ProviderRecord();
        $record->name = $overrides['name'] ?? $handle;
        $record->handle = $handle;
        $record->type = $overrides['type'] ?? self::MARKER . 'type';
        $record->enabled = $overrides['enabled'] ?? true;
        $record->settings = $overrides['settings'] ?? json_encode([
            'allowedCountries' => ['*'],
        ]);
        $record->source = 'database';

        self::assertTrue(
            $record->save(false),
            'Seeded provider must save — errors: ' . json_encode($record->getErrors()),
        );

        return $record;
    }

    /**
     * Seed a saved sms-manager {@see SenderIdRecord} pointing at a
     * previously-seeded provider record. Marker-prefixed handle so
     * `purgeTestRows()` reaches it.
     *
     * @param array<string, mixed> $overrides
     */
    protected function seedSenderId(ProviderRecord $provider, array $overrides = []): SenderIdRecord
    {
        $handle = $overrides['handle'] ?? $this->nextTestMarker(self::MARKER, 'sender');

        $record = new SenderIdRecord();
        $record->providerId = $provider->id;
        $record->providerHandle = $provider->handle;
        $record->name = $overrides['name'] ?? $handle;
        $record->handle = $handle;
        $record->senderId = $overrides['senderId'] ?? 'TestBrand';
        $record->enabled = $overrides['enabled'] ?? true;
        $record->isDev = $overrides['isDev'] ?? false;
        $record->source = 'database';

        self::assertTrue(
            $record->save(false),
            'Seeded sender ID must save — errors: ' . json_encode($record->getErrors()),
        );

        return $record;
    }

    /**
     * Drain every marker-tagged row across the tables the integration
     * touches. Done on both setUp and tearDown so a previous failed run
     * can't poison the next one.
     *
     * The integration class itself never writes a row anywhere — every
     * persistence site lives downstream in sms-manager (providers +
     * sender_ids) or upstream in Formie (formie_forms via the elements
     * table). Logs/analytics rows the dispatched send would produce are
     * NOT touched here because tests stub the SmsService away before any
     * send fires.
     */
    protected function purgeTestRows(): void
    {
        $this->purgeRowsByMarker(SenderIdRecord::tableName(), 'handle', self::MARKER);
        $this->purgeRowsByMarker(ProviderRecord::tableName(), 'handle', self::MARKER);
    }

    /**
     * Seed a real Formie Form element with controlled integration settings.
     *
     * Saved via Craft's element API (`saveElement`) with validation off —
     * Form's normal validation requires a templateId / layoutId, neither of
     * which the usage-scan tests care about. The save still routes through
     * Formie's `EVENT_BEFORE_SAVE` / `afterSave` hooks, which is what makes
     * `Form::find()->id(...)` return the seeded form on the way back out.
     *
     * Saved through {@see saveTestElement()} so base test cleanup can hard
     * delete the full element triplet (elements + elements_sites +
     * formie_forms) on tearDown.
     *
     * @param array<string, array<string, mixed>> $integrations
     *     Per-handle integration blocks the way Formie serialises them on
     *     a saved form (`['mySms' => ['enabled' => true, 'providerId' => '7'], …]`).
     */
    protected function seedFormieForm(array $integrations, ?string $titleSuffix = null): int
    {
        $form = new FormieForm();
        $form->title = $titleSuffix !== null ? self::MARKER . $titleSuffix : $this->nextTestMarker(self::MARKER, 'form');
        $form->handle = $this->nextTestMarker(self::MARKER . 'h', 'form');
        $form->siteId = Craft::$app->getSites()->getPrimarySite()->id;
        $form->setSettings(['integrations' => $integrations], false);

        $this->saveTestElement($form, false);

        return (int) $form->id;
    }
}
