# Requirements

## System Requirements

| Requirement | Version |
|-------------|---------|
| [Craft CMS](https://craftcms.com/) | 5.0+ |
| [PHP](https://php.net/) | 8.2+ |

## Dependencies

Composer pulls these packages automatically. The two Craft plugin dependencies must also be installed and enabled in the Control Panel — Formie SMS is a bridge between them and does nothing on its own.

| Package | Version | Purpose |
|---------|---------|---------|
| [verbb/formie](https://verbb.io/craft-plugins/formie) | 3.0+ | The forms plugin Formie SMS attaches to — required, install in CP |
| [lindemannrock/craft-sms-manager](https://github.com/LindemannRock/craft-sms-manager) | 5.0+ | Sends the messages and stores providers, sender IDs, logs, and analytics — required, install in CP |
| [lindemannrock/craft-plugin-base](https://github.com/LindemannRock/craft-plugin-base) | 5.0+ | Shared base plugin utilities (helpers, traits, layouts) |

The SMS integration only appears in Formie once both Formie and SMS Manager are installed and enabled. Configure at least one enabled provider and sender ID in SMS Manager before wiring up a form — see [Quickstart](quickstart.md).
