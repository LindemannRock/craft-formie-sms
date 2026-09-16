# Installation & Setup

> [!NOTE]
> Formie SMS is in active development and not yet available on the Craft Plugin Store. Install via Composer for now.

> [!IMPORTANT]
> Formie SMS needs both [Formie](https://verbb.io/craft-plugins/formie) and [SMS Manager](https://github.com/LindemannRock/craft-sms-manager) installed and enabled. Composer pulls them in automatically; install each in the Control Panel under **Settings → Plugins**.

## Composer

Add the package to your project using Composer and the command line.

1. Open your terminal and go to your Craft project:

```bash
cd /path/to/project
```

2. Then tell Composer to require the plugin, and Craft to install it:

```bash title="Composer"
composer require lindemannrock/craft-formie-sms && php craft plugin/install formie-sms
```

```bash title="DDEV"
ddev composer require lindemannrock/craft-formie-sms && ddev craft plugin/install formie-sms
```

## Post-Install Setup

Install and enable Formie and SMS Manager in the Control Panel under **Settings → Plugins**. Then configure an enabled provider and sender ID in SMS Manager before connecting a Formie form.

See [Configuration](configuration.md) if you want to rename Formie SMS in the Control Panel.

## Quick Start

See [Quickstart](quickstart.md) for the fastest path from install to your first SMS on form submission.
