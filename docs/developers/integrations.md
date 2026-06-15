# Integrations

Beyond sending, Formie SMS tells SMS Manager *where its providers and sender IDs are being used*. This is how SMS Manager stops you from deleting a provider or sender ID that a live form still depends on.

You don't configure anything here — it works automatically once both plugins are installed. This page explains what it does and where the behavior shows up.

## Usage tracking

Formie SMS registers a usage-tracking integration with SMS Manager. When an admin tries to delete a provider or a sender ID in SMS Manager, SMS Manager asks every registered integration "is this still in use?" Formie SMS answers by scanning Formie form settings:

- **Provider in use** — any enabled form whose SMS integration points at a sender belonging to that provider.
- **Sender ID in use** — any enabled form whose SMS integration uses that sender.

If a form is found, the delete is blocked and the form is listed (with a link to its Integrations tab) so you can re-point or disable it first.

> [!NOTE]
> Forms set to **Use SMS Manager default** aren't reported against a specific sender — they don't name one. SMS Manager's own guard against deleting the configured default sender covers that case.

## How it's wired

Formie SMS implements SMS Manager's `IntegrationInterface` (`getProviderUsages()` / `getSenderIdUsages()`) and registers it on SMS Manager's `EVENT_REGISTER_INTEGRATIONS` event under the handle `formie-sms`. The same scan understands both the current handle-based form data and the legacy numeric format, so usage tracking keeps working on forms saved before the [handle migration](console-commands.md#formie-smsmigrateintegration-handles).

For the SMS Manager side of this contract — implementing the interface for your own plugin and querying usages — see SMS Manager's [Integrations](https://github.com/LindemannRock/craft-sms-manager) documentation.
