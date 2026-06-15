# Features overview

Send an SMS every time a Formie form is submitted — an order confirmation, a lead alert to your sales team, a booking reminder — without writing any code. Formie SMS adds an SMS option to Formie's integrations and dispatches each message through [SMS Manager](https://github.com/LindemannRock/craft-sms-manager).

> [!TIP]
> New to Formie SMS? Start with [Installation](../get-started/installation.md) and the [Quickstart](../get-started/quickstart.md), then come back here for a tour.

## What it does

Formie SMS is a bridge, not a gateway. Formie owns the form and the submission; SMS Manager owns the providers, sender IDs, delivery logs, and analytics. Formie SMS sits between them: when a form is submitted, it reads the recipients and message you configured, resolves the sender, and hands the send to SMS Manager.

That means there's almost nothing to configure in the plugin itself. Everything you tune lives in two familiar places — the **Integrations** tab on each Formie form, and your existing SMS Manager setup.

## What you'll use it for

- Texting a confirmation to the person who submitted a form
- Alerting a team member's phone the moment a lead or support request comes in
- Sending booking, appointment, or order reminders pulled from form fields
- Running different SMS per language on a multi-site install
- Keeping every form-triggered SMS in SMS Manager's logs and analytics alongside the rest of your messaging

## Core capabilities

- **[SMS notifications](sms-notifications.md)** — The heart of the plugin: add the SMS integration to a form, then set the sender, recipients, message, and an optional language filter. Recipients and message both accept Formie variables, so each text is personalized from the submission.

- **[Phone number variables](phone-variables.md)** — How to reference a submitter's phone field in the recipients box, and why `{field:phone}` (full international format) is the portable choice across every provider.

- **Usage tracking** — Formie SMS registers with SMS Manager so a form that uses a provider or sender ID is reported as a *usage*. SMS Manager then blocks deletion of a provider or sender still wired to a live form. See [Integrations](../developers/integrations.md).

## Where things live

| You configure… | In… |
|----------------|-----|
| Sender, recipients, message, language filter | The **Integrations** tab on each Formie form |
| Providers, sender IDs, the default sender | SMS Manager |
| Delivery logs and analytics | SMS Manager → SMS Logs / Analytics |
| The plugin's display name | Settings → Plugins → Formie SMS |

![The SMS integration settings on a Formie form](images/overview-form-settings.webp)

## Next steps

1. [Install the plugin](../get-started/installation.md)
2. [Send your first SMS on submission](../get-started/quickstart.md)
3. [Tour every form-level setting](sms-notifications.md)
