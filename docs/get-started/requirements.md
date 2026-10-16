# Requirements

## System Requirements

| Requirement | Version |
|-------------|---------|
| [Craft CMS](https://craftcms.com/) | 5.11+ |
| [PHP](https://php.net/) | 8.2+ |

## Dependencies

Composer pulls these packages automatically. Craft plugin dependencies also need to be installed in the Control Panel.

| Package | Version | Purpose |
|---------|---------|---------|
| [verbb/formie](https://verbb.io/craft-plugins/formie) | 3.0+ | The forms plugin Formie SMS attaches to — required, install in CP |
| [lindemannrock/craft-sms-manager](https://github.com/LindemannRock/craft-sms-manager) | 5.18.1+ | Sends the messages and stores providers, sender IDs, logs, and analytics — required, install in CP |
| [lindemannrock/craft-plugin-base](https://github.com/LindemannRock/craft-plugin-base) | 5.39+ | Shared base plugin utilities (helpers, traits, layouts) |

The SMS integration only appears in Formie once both Formie and SMS Manager are installed and enabled. Configure at least one enabled provider and sender ID in SMS Manager before wiring up a form — see [Quickstart](quickstart.md).

SMS Manager 5.18.1 provides the sender-aware status API used by the Sender ID hold notice. If the selected provider does not expose hold status, or status cannot be read, the notice directs you to check SMS Manager.
