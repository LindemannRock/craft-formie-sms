# Twig Globals

Formie SMS provides the following global variables in your Twig templates.

## `formieSmsHelper`

*Provided by `lindemannrock/base`*

| Property | Description |
|----------|-------------|
| `formieSmsHelper.displayName` | Display name (singular, without "Manager") |
| `formieSmsHelper.pluralDisplayName` | Plural display name (without "Manager") |
| `formieSmsHelper.fullName` | Full plugin name (as configured) |
| `formieSmsHelper.lowerDisplayName` | Lowercase display name (singular) |
| `formieSmsHelper.pluralLowerDisplayName` | Lowercase plural display name |

### Examples

```twig
{{ formieSmsHelper.displayName }}
{{ formieSmsHelper.pluralDisplayName }}
{{ formieSmsHelper.fullName }}
{{ formieSmsHelper.lowerDisplayName }}
{{ formieSmsHelper.pluralLowerDisplayName }}
```

---

