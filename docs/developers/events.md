# Events

Formie SMS doesn't define events of its own — it's a Formie integration, so you hook it through Formie's submission pipeline. The useful hook is the event Formie fires right before it runs each integration on a submission, which lets you inspect, modify, or cancel a Formie SMS send.

## Cancel or alter a send

`verbb\formie\services\Submissions::EVENT_BEFORE_TRIGGER_INTEGRATION` fires once per enabled integration, just before its payload is sent. The event is cancelable — set `$event->isValid = false` to skip the send. Check `$event->integration` to act only on the SMS integration:

```php
use lindemannrock\formiesms\integrations\miscellaneous\Sms;
use verbb\formie\events\TriggerIntegrationEvent;
use verbb\formie\services\Submissions;
use yii\base\Event;

Event::on(
    Submissions::class,
    Submissions::EVENT_BEFORE_TRIGGER_INTEGRATION,
    function(TriggerIntegrationEvent $event) {
        // Only act on the Formie SMS integration
        if (!$event->integration instanceof Sms) {
            return;
        }

        $submission = $event->submission;

        // Example: don't send for submissions missing a phone field
        if (empty($submission->getFieldValue('phone'))) {
            $event->isValid = false;
        }
    }
);
```

### Event properties

| Property | Type | Description |
|----------|------|-------------|
| `submission` | `Submission` | The submission being processed |
| `integration` | `Integration` | The integration about to run — check `instanceof Sms` |
| `type` | `string` | The integration's class name |
| `isValid` | `bool` | Set to `false` to cancel this integration's send |

> [!NOTE]
> Formie's generic `EVENT_BEFORE_SEND_PAYLOAD` / `EVENT_AFTER_SEND_PAYLOAD` events (on `verbb\formie\base\Integration`) do **not** fire for Formie SMS — the integration dispatches through SMS Manager directly rather than through Formie's HTTP payload flow. Use `EVENT_BEFORE_TRIGGER_INTEGRATION` above.

## Where to put this

Register the listener in a custom module's `init()` (or a plugin's). See [Craft's events documentation](https://craftcms.com/docs/5.x/extend/events.html) for module setup.
