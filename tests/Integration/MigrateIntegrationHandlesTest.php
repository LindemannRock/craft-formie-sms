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
use craft\db\Query;
use lindemannrock\formiesms\console\controllers\MigrateController;
use lindemannrock\formiesms\tests\Stubs\StubSenderIdsService;
use lindemannrock\formiesms\tests\TestCase;

/**
 * Verifies legacy sender migration against forms owned by this test.
 */
final class MigrateIntegrationHandlesTest extends TestCase
{
    private StubSenderIdsService $stubSenderIds;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stubSenderIds = new StubSenderIdsService();
        $this->swapPluginComponent('sms-manager', 'senderIds', $this->stubSenderIds);
    }

    public function testMigratesResolvableBlockAndReportsDanglingBlockOnFirstPass(): void
    {
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider);
        $this->stubSenderIds->byId[(int) $sender->id] = $sender;
        $danglingId = 999999999;
        $formId = $this->seedFormieForm([
            'readySms' => ['senderIdId' => (string) $sender->id, 'providerId' => (string) $provider->id],
            'missingSms' => ['senderIdId' => (string) $danglingId],
        ]);
        $otherFormId = $this->seedFormieForm(['otherSms' => ['senderIdId' => (string) $sender->id]]);
        $otherSettings = $this->settings($otherFormId);

        $controller = new class('migrate', Craft::$app) extends MigrateController {
            public int $ownedFormId;
            public string $outputText = '';

            protected function candidateForms(): array
            {
                return (new Query())
                    ->select(['id', 'settings'])
                    ->from('{{%formie_forms}}')
                    ->where(['id' => $this->ownedFormId])
                    ->andWhere(['like', 'settings', '"senderIdId":'])
                    ->all();
            }

            public function stdout($string)
            {
                $this->outputText .= $string;
                return strlen($string);
            }
        };
        $controller->ownedFormId = $formId;
        self::assertSame(0, $controller->actionIntegrationHandles());
        self::assertStringContainsString('Found 1 candidate form(s).', $controller->outputText);
        self::assertStringContainsString(sprintf('[form %d] Updated 1 block(s):', $formId), $controller->outputText);
        self::assertStringContainsString(sprintf('"readySms" (senderIdId=%d → senderIdHandle="%s")', $sender->id, $sender->handle), $controller->outputText);
        self::assertStringContainsString(sprintf('Dangling senderIdId(s): %d', $danglingId), $controller->outputText);
        self::assertStringContainsString('Migrated: 1, Already current: 0, Unresolved (dangling): 1, Errored: 0', $controller->outputText);
        self::assertSame($otherSettings, $this->settings($otherFormId));
        $settings = json_decode($this->settings($formId), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame($sender->handle, $settings['integrations']['readySms']['senderIdHandle']);
        self::assertSame((string) $sender->id, $settings['integrations']['readySms']['senderIdId']);
        self::assertSame((string) $provider->id, $settings['integrations']['readySms']['providerId']);
        self::assertSame((string) $danglingId, $settings['integrations']['missingSms']['senderIdId']);
        self::assertArrayNotHasKey('senderIdHandle', $settings['integrations']['missingSms']);

        $afterFirst = $this->settings($formId);
        $second = $this->migrate($formId);
        self::assertSame('unresolved', $second['status']);
        self::assertSame($afterFirst, $this->settings($formId));
    }

    public function testCurrentAndEmptyReferencesRemainUnchanged(): void
    {
        $formId = $this->seedFormieForm([
            'currentSms' => ['senderIdId' => '123', 'senderIdHandle' => 'existing'],
            'defaultSms' => ['senderIdId' => null],
        ]);
        $before = $this->settings($formId);
        self::assertSame('already_current', $this->migrate($formId)['status']);
        self::assertSame($before, $this->settings($formId));
    }

    public function testResolvableOnlyMigrationIsIdempotent(): void
    {
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider);
        $this->stubSenderIds->byId[(int) $sender->id] = $sender;
        $formId = $this->seedFormieForm(['sms' => ['senderIdId' => (string) $sender->id]]);
        self::assertSame('migrated', $this->migrate($formId)['status']);
        $after = $this->settings($formId);
        self::assertSame('already_current', $this->migrate($formId)['status']);
        self::assertSame($after, $this->settings($formId));
    }

    public function testMalformedSettingsReportAnErrorWithoutWriting(): void
    {
        $formId = $this->seedFormieForm([]);
        $before = $this->settings($formId);
        $method = new \ReflectionMethod(MigrateController::class, 'migrateOne');
        $result = $method->invoke(new MigrateController('migrate', Craft::$app), $formId, '{invalid');
        self::assertSame('errored', $result['status']);
        self::assertSame($before, $this->settings($formId));
    }

    public function testDatabaseWriteFailurePreservesTheForm(): void
    {
        $provider = $this->seedProvider();
        $sender = $this->seedSenderId($provider);
        $this->stubSenderIds->byId[(int) $sender->id] = $sender;
        $formId = $this->seedFormieForm(['sms' => ['senderIdId' => (string) $sender->id]]);
        $before = $this->settings($formId);
        $controller = new class('migrate', Craft::$app) extends MigrateController {
            protected function updateFormSettings(int $formId, string $newJson): void
            {
                throw new \RuntimeException('test write failure');
            }
        };
        $method = new \ReflectionMethod(MigrateController::class, 'migrateOne');
        $result = $method->invoke($controller, $formId, $before);
        self::assertSame('errored', $result['status']);
        self::assertStringContainsString('DB update failed', $result['details']);
        self::assertSame($before, $this->settings($formId));
    }

    /** @return array{status: string, details: ?string} */
    private function migrate(int $formId): array
    {
        $method = new \ReflectionMethod(MigrateController::class, 'migrateOne');
        return $method->invoke(new MigrateController('migrate', Craft::$app), $formId, $this->settings($formId));
    }

    private function settings(int $formId): string
    {
        return (string) (new Query())->select('settings')->from('{{%formie_forms}}')->where(['id' => $formId])->scalar();
    }
}
