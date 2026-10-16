<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Integration;

use Craft;
use craft\web\View;
use lindemannrock\base\helpers\DateFormatHelper;
use lindemannrock\formiesms\integrations\miscellaneous\Sms;
use lindemannrock\formiesms\tests\Support\StatusProvider;
use lindemannrock\formiesms\tests\Support\StatusTransport;
use lindemannrock\formiesms\tests\Support\TransportIsolation;
use lindemannrock\formiesms\tests\TestCase;
use lindemannrock\smsmanager\records\ProviderRecord;
use lindemannrock\smsmanager\records\SenderIdRecord;
use lindemannrock\smsmanager\services\ProvidersService;
use lindemannrock\smsmanager\services\SenderIdsService;
use lindemannrock\smsmanager\SmsManager;
use verbb\formie\elements\Form;

/**
 * Sender-specific, read-only hold notices with safe unavailable fallback.
 * @since 3.11.3
 */
final class SmsSenderHoldNoticeTest extends TestCase
{
    private mixed $originalTransport;
    private StatusTransport $transport;
    private StatusProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTransport = SmsManager::$plugin->getComponents()['transport'] ?? null;
        $this->transport = new StatusTransport();
        SmsManager::$plugin->set('transport', $this->transport);
        $this->provider = new StatusProvider();
        $providers = $this->createMock(ProvidersService::class);
        $record = new ProviderRecord(['handle' => 'synthetic', 'name' => 'Synthetic', 'enabled' => true, 'type' => 'synthetic-status', 'settings' => json_encode(['apiKey' => 'private-canary'])]);
        $providers->method('getProviderByHandle')->willReturn($record);
        $providers->method('getAllProviders')->willReturn([$record]);
        $providers->method('createProviderByType')->willReturn($this->provider);
        $this->swapPluginComponent('sms-manager', 'providers', $providers);
        $senders = $this->createMock(SenderIdsService::class);
        $sender = new SenderIdRecord(['handle' => 'synthetic', 'providerHandle' => 'synthetic', 'name' => 'Synthetic sender', 'enabled' => true, 'isDev' => true]);
        $senders->method('getDefaultSenderId')->willReturn($sender);
        $senders->method('getAllSenderIds')->willReturn([$sender]);
        $senders->method('getSenderIdByHandle')->willReturn($sender);
        $senders->method('isDevelopmentSender')->willReturn(true);
        $this->swapPluginComponent('sms-manager', 'senderIds', $senders);
    }

    protected function tearDown(): void
    {
        SmsManager::$plugin->set('transport', $this->originalTransport);
        parent::tearDown();
    }

    public function testTimedHoldUsesProviderLaneAndSafeFormattedRecovery(): void
    {
        $date = new \DateTime('+10 minutes');
        $this->transport->result['holds'] = [$this->hold('main', $date)];
        // Provider decides a development sender falls back to the main account.
        $this->provider->lane = 'main';
        $notices = $this->notices();
        self::assertSame($notices['synthetic'], $notices['']);
        self::assertTrue($notices['synthetic']['blocked']);
        self::assertStringContainsString(DateFormatHelper::formatDatetime($date), $notices['synthetic']['message']);
        self::assertTrue($this->provider->receivedSettings['isDev']);
        self::assertStringNotContainsString('private-canary', json_encode($notices));
        self::assertStringNotContainsString('private-token', json_encode($notices));
    }

    public function testIndependentDevelopmentAccountIgnoresMainHold(): void
    {
        $this->provider->lane = 'development';
        $this->transport->result['holds'] = [$this->hold('main', null)];
        self::assertSame([], $this->notices());
    }

    public function testTransportManualHoldAppliesToDevelopmentAccount(): void
    {
        $this->provider->lane = 'development';
        $this->transport->result['holds'] = [$this->hold('transport', null)];
        $notice = $this->notices()['synthetic'];
        self::assertTrue($notice['blocked']);
        self::assertSame('SMS sending is paused until this hold is released in SMS Manager.', $notice['message']);
    }

    public function testExpiredHoldsAndAvailableSenderHaveNoNotice(): void
    {
        self::assertSame([], $this->notices());
        $hold = $this->hold('main', new \DateTime('-1 minute'));
        $hold['active'] = false;
        $this->transport->result['holds'] = [$hold];
        self::assertSame([], $this->notices());
    }

    public function testUnavailableStatusDoesNotClaimSendingIsAvailable(): void
    {
        $this->transport->result['available'] = false;
        $notice = $this->notices()['synthetic'];
        self::assertFalse($notice['blocked']);
        self::assertSame('Hold status is unavailable here. Check SMS Manager.', $notice['message']);
    }

    public function testOlderSmsManagerWithoutStatusCapabilityUsesSafeFallback(): void
    {
        SmsManager::$plugin->set('transport', null);
        self::assertSame('Hold status is unavailable here. Check SMS Manager.', $this->notices()['synthetic']['message']);
    }

    public function testSettingsRenderSafeInlineSameTabManagerLink(): void
    {
        $this->transport->result['holds'] = [$this->hold('transport', null)];
        $form = new Form();
        $form->setSettings(['integrations' => ['synthetic' => ['senderIdHandle' => 'synthetic']]], false);
        $view = Craft::$app->getView();
        $mode = $view->getTemplateMode();
        try {
            $view->setTemplateMode(View::TEMPLATE_MODE_CP);
            $html = (new Sms(['handle' => 'synthetic']))->getFormSettingsHtml($form);
        } finally {
            $view->setTemplateMode($mode);
        }
        self::assertStringContainsString('Affected submissions will not send automatically later.', strip_tags($html));
        self::assertStringNotContainsString('private-canary', $html);
        self::assertStringNotContainsString('private-token', $html);
        $document = new \DOMDocument();
        @$document->loadHTML($html);
        $links = (new \DOMXPath($document))->query('//a[contains(@href, "sms-manager/providers")]');
        self::assertGreaterThan(0, $links->length);
        foreach ($links as $link) {
            self::assertInstanceOf(\DOMElement::class, $link);
            self::assertSame('SMS Manager', $link->textContent);
            self::assertSame('', $link->getAttribute('target'));
            self::assertStringContainsString('SMS sending is paused until this hold is released in SMS Manager.', $link->parentNode->textContent);
        }
    }

    private function notices(): array
    {
        $count = TransportIsolation::$interceptedRequests;
        $method = new \ReflectionMethod(Sms::class, 'buildSenderHoldNotices');
        $result = $method->invoke(new Sms(), [['value' => 'synthetic'], ['value' => '']]);
        self::assertSame($count, TransportIsolation::$interceptedRequests);
        return $result;
    }

    private function hold(string $lane, ?\DateTime $recovery): array
    {
        return ['scope' => $lane === 'transport' ? 'transport' : 'account', 'lane' => $lane, 'active' => true, 'recoveryAt' => $recovery, 'token' => 'private-token'];
    }
}
