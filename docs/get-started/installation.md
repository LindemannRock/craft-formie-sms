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

## Copy Config File (Optional)

To rename the plugin in the Control Panel from a config file, copy the sample config to your project:

```bash
cp vendor/lindemannrock/craft-formie-sms/src/config.php config/formie-sms.php
```

See [Configuration](configuration.md) for the available options.

## Quick Start

See [Quickstart](quickstart.md) for the fastest path from install to your first SMS on form submission.
