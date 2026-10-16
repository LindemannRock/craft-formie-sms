<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Integration;

use Craft;
use craft\db\Query;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use lindemannrock\formiesms\integrations\miscellaneous\Sms;
use lindemannrock\formiesms\tests\Support\RecordingSmsService;
use lindemannrock\formiesms\tests\Support\TransportIsolation;
use lindemannrock\formiesms\tests\TestCase;
use lindemannrock\smsmanager\records\SenderIdRecord;
use lindemannrock\smsmanager\services\ProvidersService;
use lindemannrock\smsmanager\services\SenderIdsService;
use lindemannrock\smsmanager\SmsManager;
use PHPUnit\Framework\Attributes\DataProvider;
use verbb\formie\elements\Submission;
use verbb\formie\Formie;
use verbb\formie\jobs\TriggerIntegration;
use yii\queue\ExecEvent;
use yii\queue\RetryableJobInterface;
use yii\queue\sync\Queue;

/**
 * Real SMS Manager dispatch and admission with intercepted provider HTTP.
 * @since 3.11.3
 */
final class SmsTransportHoldTest extends TestCase
{
    private RecordingSmsService $smsService;
    private SenderIdRecord $mainSender;
    private SenderIdRecord $devSender;
    private array $settingsSnapshot;
    private array $requests = [];
    private int $requestStart;
    private \craft\services\Config $originalConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConfig = Craft::$app->getConfig();
        Craft::$app->set('config', new \lindemannrock\formiesms\tests\Support\SyntheticProviderConfig());
        $this->clearShadows();
        $this->settingsSnapshot = SmsManager::$plugin->getSettings()->getAttributes();
        SmsManager::$plugin->getSettings()->enableSmsLogs = true;
        SmsManager::$plugin->getSettings()->enableAnalytics = false;
        $this->swapPluginComponent('sms-manager', 'providers', new ProvidersService());
        $this->swapPluginComponent('sms-manager', 'senderIds', new SenderIdsService());
        $this->smsService = new RecordingSmsService();
        $this->swapPluginComponent('sms-manager', 'sms', $this->smsService);
        $provider = $this->seedProvider(['type' => 'mpp-sms', 'settings' => json_encode([
            'apiKey' => 'synthetic-main', 'devApiKey' => 'synthetic-development', 'allowedCountries' => ['*'],
            'apiUrl' => 'https://gateway.example.test/send',
        ])]);
        $this->mainSender = $this->seedSenderId($provider, ['senderId' => 'SyntheticMain']);
        $this->devSender = $this->seedSenderId($provider, ['senderId' => 'SyntheticDev', 'isDev' => true]);
        $this->requestStart = TransportIsolation::$interceptedRequests;
    }

    protected function tearDown(): void
    {
        try {
            TransportIsolation::$handler = null;
            Craft::$app->set('config', $this->originalConfig);
            SmsManager::$plugin->getSettings()->setAttributes($this->settingsSnapshot, false);
            $this->clearShadows();
        } finally {
            parent::tearDown();
        }
    }

    #[DataProvider('holdResponses')]
    public function testFirstHoldFailureStopsRemainingRecipients(string $body, string $scope): void
    {
        $this->respond([$body]);
        self::assertFalse($this->integration()->sendPayload($this->submission()));
        self::assertCount(1, $this->smsService->calls);
        self::assertSame($scope, $this->smsService->results[0]['failureScope']);
        self::assertSame(1, TransportIsolation::$interceptedRequests - $this->requestStart);
        self::assertSame(1, (int)(new Query())->from('{{%smsmanager_transportguards}}')->count());
    }

    public static function holdResponses(): iterable
    {
        yield 'account' => ['error,user is inactive', 'account'];
        yield 'transport' => ['error,your ip is blocked until next 10 minute(s): 192.0.2.1', 'transport'];
    }

    public function testExistingHoldStopsWithoutProviderHttp(): void
    {
        $this->respond(['error,user is inactive']);
        $this->smsService->sendWithHandleDetails('+12025550120', 'Synthetic setup', $this->mainSender->handle);
        $this->smsService->calls = [];
        $before = TransportIsolation::$interceptedRequests;
        TransportIsolation::$handler = null;
        self::assertFalse($this->integration()->sendPayload($this->submission()));
        self::assertCount(1, $this->smsService->calls);
        self::assertSame('cooldown-active', $this->smsService->results[1]['failureCode']);
        self::assertSame($before, TransportIsolation::$interceptedRequests);
    }

    public function testRecipientFailureContinuesAndPreservesSuccessfulSend(): void
    {
        $this->respond(['error,invalid mobile', 'OK,smsid:synthetic-second,mobiles:1', 'OK,smsid:synthetic-third,mobiles:1']);
        self::assertFalse($this->integration()->sendPayload($this->submission()));
        self::assertCount(3, $this->smsService->calls);
        self::assertSame([false, true, true], array_column($this->smsService->results, 'success'));
        self::assertSame(3, TransportIsolation::$interceptedRequests - $this->requestStart);
        self::assertSame(0, (int)(new Query())->from('{{%smsmanager_transportguards}}')->count());
    }

    public function testPartialSuccessIsNotReplayedAndAttributionIsPreserved(): void
    {
        $this->respond(['OK,smsid:synthetic-first,mobiles:1', 'error,user is inactive']);
        $submission = $this->submission();
        $queueBefore = (new Query())->from('{{%queue}}')->all();
        self::assertFalse(Formie::$plugin->getSubmissions()->sendIntegrationPayload($this->integration(), $submission));
        self::assertCount(2, $this->smsService->calls);
        self::assertSame([true, false], array_column($this->smsService->results, 'success'));
        self::assertSame(['+12025550121', '+12025550122'], array_column($this->smsService->calls, 'to'));
        self::assertSame('formie-sms', $this->smsService->calls[0]['sourcePlugin']);
        self::assertSame($submission->id, $this->smsService->calls[0]['sourceElementId']);
        self::assertSame($submission->siteId, $this->smsService->calls[0]['siteId']);
        self::assertSame($submission->getSite()->getLocale()->getLanguageID(), $this->smsService->calls[0]['language']);
        self::assertSame(1, (int)(new Query())->from('{{%smsmanager_logs}}')->where(['status' => 'sent'])->count());
        self::assertSame($queueBefore, (new Query())->from('{{%queue}}')->all());
        self::assertSame(2, TransportIsolation::$interceptedRequests - $this->requestStart);
    }

    public function testMainHoldDoesNotBlockIndependentDevelopmentSender(): void
    {
        $this->respond(['error,user is inactive', 'OK,smsid:synthetic-dev,mobiles:1']);
        self::assertFalse($this->integration()->sendPayload($this->submission()));
        $dev = $this->integration();
        $dev->senderIdHandle = $this->devSender->handle;
        $dev->recipients = '+12025550124';
        self::assertTrue($dev->sendPayload($this->submission()));
        self::assertSame(['synthetic-main', 'synthetic-development'], array_column($this->requests, 'apikey'));
        self::assertSame(['SyntheticMain', 'SyntheticDev'], array_column($this->requests, 'sender'));
        self::assertSame($this->devSender->handle, $this->smsService->calls[array_key_last($this->smsService->calls)]['senderIdHandle']);
    }

    public function testHoldReportsOnePrivateSafeTranslatedIntegrationError(): void
    {
        $logger = Craft::getLogger();
        $messages = $logger->messages;
        $interval = $logger->flushInterval;
        $language = Craft::$app->language;
        try {
            $logger->messages = [];
            $logger->flushInterval = PHP_INT_MAX;
            Craft::$app->language = 'de';
            $this->respond(['error,your ip is blocked until next 10 minute(s): 192.0.2.1']);
            self::assertFalse($this->integration()->sendPayload($this->submission()));
            $errors = array_filter($logger->messages, static fn(array $entry): bool => $entry[2] === 'verbb\\formie\\base\\Integration::error');
            self::assertCount(1, $errors);
            $text = json_encode($errors, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $translations = require dirname(__DIR__, 2) . '/src/translations/de/formie-sms.php';
            $key = 'SMS Manager has blocked sending for this account or transport. Remaining recipients were not processed. Check SMS Manager before retrying.';
            self::assertNotSame($key, $translations[$key]);
            self::assertStringContainsString($translations[$key], $text);
            foreach (['1202555', '192.0.2.1', 'synthetic-main', 'Synthetic message', 'apikey', 'error,your ip'] as $private) {
                self::assertStringNotContainsString($private, $text);
            }
        } finally {
            $logger->messages = $messages;
            $logger->flushInterval = $interval;
            Craft::$app->language = $language;
        }
    }

    public function testFormieJobFailureUsesQueuePolicyWithoutReplayingRecipients(): void
    {
        $submission = $this->submission();
        $submission->id = null;
        $submission->formId = $this->seedFormieForm([]);
        $this->saveTestElement($submission, false);
        $job = new TriggerIntegration(['submissionId' => $submission->id, 'integration' => $this->integration()]);
        self::assertNotInstanceOf(RetryableJobInterface::class, $job);
        $this->respond(['OK,smsid:synthetic-first,mobiles:1', 'error,user is inactive']);
        $language = Craft::$app->language;
        $locale = Craft::$app->getLocale();
        $site = Craft::$app->getSites()->getCurrentSite();
        try {
            try {
                // Invoke only this synthetic job directly; never run a queue worker.
                $job->execute(new Queue());
                self::fail('Formie must report the false integration result as a failed job.');
            } catch (\Exception $error) {
                self::assertCount(2, $this->smsService->calls);
                $queue = new Queue();
                $event = new ExecEvent(['job' => $job, 'attempt' => 1, 'error' => $error]);
                self::assertTrue($queue->handleError($event));
                self::assertFalse($event->retry);
                $queue->attempts = 2;
                self::assertFalse($queue->handleError($event));
                self::assertTrue($event->retry, 'Custom queue retry policy can replay a submission; do not execute it.');
                self::assertSame(2, TransportIsolation::$interceptedRequests - $this->requestStart);
            }
        } finally {
            Craft::$app->language = $language;
            Craft::$app->set('locale', $locale);
            Craft::$app->getSites()->setCurrentSite($site);
        }
    }

    private function respond(array $bodies): void
    {
        TransportIsolation::$handler = new MockHandler(array_map(fn(string $body): callable => function($request) use ($body): Response {
            parse_str($request->getUri()->getQuery(), $params);
            $this->requests[] = $params;
            return new Response(200, [], $body);
        }, $bodies));
    }

    private function integration(): Sms
    {
        return new class(['senderIdHandle' => $this->mainSender->handle, 'recipients' => '+12025550121,+12025550122,+12025550123', 'message' => 'Synthetic message']) extends Sms {
            protected function renderMessage(string $template, Submission $submission): string
            {
                return $template;
            }
        };
    }

    private function submission(): Submission
    {
        return new Submission(['id' => 999999, 'siteId' => Craft::$app->getSites()->getPrimarySite()->id]);
    }

    private function clearShadows(): void
    {
        self::assertInstanceOf(\lindemannrock\formiesms\tests\Support\IsolatedQueue::class, Craft::$app->getQueue());
        foreach (['transportguards', 'transportreleases', 'logs', 'analytics'] as $name) {
            Craft::$app->getDb()->createCommand()->delete('{{%smsmanager_' . $name . '}}')->execute();
        }
    }
}
