# Configuration

Formie SMS has almost nothing to configure on its own — the providers, sender IDs, recipients, and message content all live in [SMS Manager](https://github.com/LindemannRock/craft-sms-manager) and on each Formie form. The only plugin-level setting is its display name.

Set it in the Control Panel at **Settings → Plugins → Formie SMS**, or lock it per environment with a config file.

## Settings

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `pluginName` | `string` | `'Formie SMS'` | The name shown in the Control Panel menu and on the Formie integration |

## Config file

Copy `vendor/lindemannrock/craft-formie-sms/src/config.php` to `config/formie-sms.php` and override the value there. A value set in the config file takes precedence over the Control Panel field, which is then shown as read-only.

```php
// config/formie-sms.php
return [
    'pluginName' => 'SMS Notifications',
];
```

Like all Craft config files, it supports per-environment overrides:

```php
// config/formie-sms.php
return [
    '*' => [
        'pluginName' => 'Formie SMS',
    ],
    'production' => [
        'pluginName' => 'SMS Notifications',
    ],
];
```

## Where the rest lives

| To configure… | Go to… |
|---------------|--------|
| Providers, sender IDs, the default sender | SMS Manager → Providers / Sender IDs |
| Delivery logs and analytics | SMS Manager → SMS Logs / Analytics |
| Recipients, message, sender, language filter | The **Integrations** tab on each Formie form — see [SMS notifications](../feature-tour/sms-notifications.md) |
