# Shared Features

Formie SMS uses the following shared libraries and features.

## `lindemannrock/base`

| Feature | Description |
|---------|-------------|
| `PluginHelper::bootstrap()` | Registers the base module and Twig global helpers, and configures the install experience |
| `SettingsConfigTrait` | Config file override detection |
| `SettingsDisplayNameTrait` | Standardized plugin name helper methods |
| `PluginNameSettingsTrait` | The `pluginName` setting, its validation rule, and label |

### Details

**PluginHelper::bootstrap()**

Provides the plugin name helpers available in Twig templates (see [Twig Globals](twig-globals.md)).

**SettingsConfigTrait**

Settings can be overridden via `config/formie-sms.php` — see [Configuration](../get-started/configuration.md).

**SettingsDisplayNameTrait**

Provides `getDisplayName()`, `getFullName()`, `getPluralDisplayName()`, etc.

**PluginNameSettingsTrait**

Supplies the shared `pluginName` setting, its validation rule, and its translated label.

---

