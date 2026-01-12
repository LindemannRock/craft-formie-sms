# Formie SMS Plugin for Craft CMS

[![Latest Version](https://img.shields.io/packagist/v/lindemannrock/craft-formie-sms.svg)](https://packagist.org/packages/lindemannrock/craft-formie-sms)
[![Craft CMS](https://img.shields.io/badge/Craft%20CMS-5.0+-orange.svg)](https://craftcms.com/)
[![Formie](https://img.shields.io/badge/Formie-3.0+-purple.svg)](https://verbb.io/craft-plugins/formie)
[![SMS Manager](https://img.shields.io/badge/SMS%20Manager-5.0+-green.svg)](https://github.com/LindemannRock/craft-sms-manager)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net/)
[![License](https://img.shields.io/packagist/l/lindemannrock/craft-formie-sms.svg)](LICENSE)

A Craft CMS plugin that integrates Verbb's Formie with SMS Manager, enabling SMS notifications on form submission with multi-provider support and language filtering.

## Beta Notice

This plugin is currently in active development and provided under the MIT License for testing purposes.

**Licensing is subject to change.** We are finalizing our licensing structure and some or all features may require a paid license when officially released on the Craft Plugin Store. Some plugins may remain free, others may offer free and Pro editions, or be fully commercial.

If you are using this plugin, please be aware that future versions may have different licensing terms.

## Requirements

- Craft CMS 5.0 or greater
- PHP 8.2 or greater
- [Formie](https://verbb.io/craft-plugins/formie) 3.0 or greater
- [SMS Manager](https://github.com/LindemannRock/craft-sms-manager) 5.0 or greater

## Features

- **SMS Notifications**: Send SMS messages when forms are submitted
- **Multi-Provider Support**: Use any SMS provider configured in SMS Manager
- **Multiple Sender IDs**: Select sender IDs per form with provider-based filtering
- **Dynamic Recipients**: Use form field values as recipient phone numbers
- **Rich Message Content**: Include form field data in SMS messages using Formie's variable system
- **Language Filtering**: Only send SMS for submissions from specific language sites
- **RTL Support**: Automatic right-to-left text direction for Arabic messages
- **Analytics Integration**: SMS usage tracked in SMS Manager's analytics

## Installation

### Via Composer

```bash
cd /path/to/project
```

```bash
composer require lindemannrock/craft-formie-sms
```

```bash
./craft plugin/install formie-sms
```

### Using DDEV

```bash
cd /path/to/project
```

```bash
ddev composer require lindemannrock/craft-formie-sms
```

```bash
ddev craft plugin/install formie-sms
```

### Via Control Panel

In the Control Panel, go to Settings → Plugins and click "Install" for Formie SMS.

## Configuration

### Prerequisites

Before using Formie SMS, ensure SMS Manager is configured with:

1. At least one **enabled provider** (e.g., MPP-SMS)
2. At least one **enabled sender ID** linked to that provider

### Step 1: Create SMS Integration

1. Go to **Formie → Settings → Integrations**
2. Click **New Integration** and select the integration (displays as your SMS Manager plugin name) under **Miscellaneous**
3. Configure the integration:
   - **Name**: Give your integration a name (e.g., "SMS Notifications")
   - **Handle**: Unique identifier (auto-generated or custom)
4. Save the integration

### Step 2: Enable for Forms

1. Edit your Formie form
2. Go to the **Integrations** tab
3. Enable your SMS integration
4. Configure the SMS settings:

**Provider**: Select which SMS provider to use from your SMS Manager providers.

**Sender ID**: Select the sender ID (filters automatically based on selected provider).

**Recipient(s)**: Enter phone numbers or use form field variables:
- Static: `+96512345678`
- Dynamic: `{field.phoneNumber}`
- Multiple: `+96512345678, {field.alternatePhone}`

**Message**: Compose your SMS using Formie's rich text editor with variable tags:
```
New form submission from {field.name}!
Email: {field.email}
Phone: {field.phone}
```

**Language Filter**: Choose when to send SMS:
- **Any Language**: Send for all submissions
- **Specific Language**: Only send when form is submitted from a specific site language (e.g., English, Arabic)

5. Save the form

## Usage

### Dynamic Recipients

Use Formie's variable system to pull recipient numbers from form fields:

```
{field.phoneNumber}
```

For multiple recipients, separate with commas:
```
{field.primaryPhone}, {field.secondaryPhone}, +96512345678
```

### Message Templates

Use any form field in your message:

```
Thank you {field.firstName}!

Your order #{field.orderNumber} has been received.

We will contact you at {field.email}.
```

### Language Filtering

When "Language Filter" is set to a specific language:
- SMS is **only sent** when the form is submitted from a site matching that language
- Useful for multi-site setups where you want different SMS behavior per language
- Set to "Any Language" to send for all submissions regardless of site

### Provider and Sender ID Selection

The Sender ID dropdown automatically filters based on the selected Provider:
- Selecting a Provider shows only Sender IDs belonging to that provider
- Changing Provider updates available Sender IDs
- Both must be selected for the integration to work

## How It Works

1. User submits a Formie form
2. Formie triggers the SMS integration
3. Plugin checks language filter (if configured)
4. Recipient phone numbers are parsed from the settings (with variables resolved)
5. Message content is rendered with form field values
6. SMS is sent via SMS Manager using the configured provider and sender ID
7. Delivery is logged in SMS Manager's SMS Logs
8. Analytics are updated in SMS Manager's Analytics

## Multi-Site / Multi-Language

For multi-site installations with different languages:

**Example Setup:**
- Site 1: English (en)
- Site 2: Arabic (ar)

**Use Case 1: Different SMS for each language**
- Create two SMS integrations (one per language)
- Set Language Filter to the respective language
- Configure different messages for each

**Use Case 2: SMS only for Arabic submissions**
- Create one SMS integration
- Set Language Filter to "ar"
- Arabic site submissions get SMS, English submissions don't

**Use Case 3: SMS for all submissions**
- Create one SMS integration
- Set Language Filter to "Any Language"
- All submissions trigger SMS regardless of site

## Analytics

All SMS sent through Formie SMS are tracked in SMS Manager:

- **SMS Logs**: Full delivery history with message content
- **Analytics**: Aggregated statistics by date, provider, sender ID
- **Source Tracking**: Shows "formie-sms" as source plugin

View analytics at **SMS Manager → Analytics** and logs at **SMS Manager → SMS Logs**.

## Troubleshooting

### Integration Not Appearing

- Ensure both **Formie** and **SMS Manager** plugins are installed and enabled
- Clear all caches: `php craft clear-caches/all`
- Check plugin is enabled in Settings → Plugins

### No Providers or Sender IDs Available

- Verify SMS Manager has at least one enabled provider
- Verify SMS Manager has at least one enabled sender ID linked to an enabled provider
- Check the provider/sender ID is not disabled

### SMS Not Sending

1. **Check Language Filter**: If set to a specific language, verify form was submitted from matching site
2. **Check Recipients**: Ensure phone numbers are valid and properly formatted
3. **Check Provider Status**: Verify provider is enabled in SMS Manager
4. **Check SMS Manager Logs**: View logs at SMS Manager → SMS Logs for error details
5. **Check System Logs**: Review storage/logs for detailed error messages

### Message Content Empty

- Ensure field handles match exactly (case-sensitive)
- Use the variable picker in the message editor
- Test with a simple static message first

### Sender ID Dropdown Stays Disabled

- Select a Provider first
- Ensure the selected Provider has at least one enabled Sender ID

## Events

The plugin fires standard Formie integration events:

```php
use verbb\formie\events\SendIntegrationPayloadEvent;
use verbb\formie\services\Integrations;
use yii\base\Event;

Event::on(
    Integrations::class,
    Integrations::EVENT_BEFORE_SEND_PAYLOAD,
    function(SendIntegrationPayloadEvent $event) {
        // Access the submission
        $submission = $event->submission;

        // Check if it's our SMS integration
        if ($event->integration instanceof \lindemannrock\formiesms\integrations\miscellaneous\Sms) {
            // Modify or cancel the send
            // $event->isValid = false; // Cancel
        }
    }
);
```

## Support

- **Documentation**: [https://github.com/LindemannRock/craft-formie-sms](https://github.com/LindemannRock/craft-formie-sms)
- **Issues**: [https://github.com/LindemannRock/craft-formie-sms/issues](https://github.com/LindemannRock/craft-formie-sms/issues)
- **Email**: [support@lindemannrock.com](mailto:support@lindemannrock.com)

## License

This plugin is licensed under the MIT License. See [LICENSE](LICENSE) for details.

---

Developed by [LindemannRock](https://lindemannrock.com)

Built for use with [Formie](https://verbb.io/craft-plugins/formie) by Verbb and [SMS Manager](https://github.com/LindemannRock/craft-sms-manager)
