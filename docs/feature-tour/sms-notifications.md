# SMS notifications

Send a text message when a form is submitted. You add the SMS integration to a Formie form, tell it who to text and what to say, and Formie SMS dispatches the message through SMS Manager on every submission.

The recipients and the message both accept Formie variables, so each SMS is built from the submission — text the person who filled in the form, or alert your team with the details they entered.

## What you'll use it for

- Confirming to a submitter that their enquiry, order, or booking was received
- Notifying a staff phone the instant a high-priority form comes in
- Sending a one-time code, reference number, or reminder drawn from form fields
- Routing different messages per language on a multi-site setup

## Before you start

Formie SMS sends through SMS Manager, so configure SMS Manager first:

1. At least one **enabled provider**.
2. At least one **enabled sender ID** linked to that provider.

See SMS Manager's own docs for [providers](https://github.com/LindemannRock/craft-sms-manager) and sender IDs. Then come back to Formie.

## Create the integration

In the Control Panel — no code:

1. Go to **Formie → Settings → Integrations** and click **New Integration**.
2. Under **Miscellaneous**, choose the SMS Manager integration. It's labelled with your SMS Manager plugin name and described as "Send SMS notifications via SMS Manager on form submission."

   ![Choosing the SMS integration under Miscellaneous](images/sms-notifications-new-integration.webp)

3. Give it a **Name** (e.g. "SMS Notifications") — Formie generates the handle for you — and **Save**.

There's nothing else to set on the integration itself. Its settings page just links you back to SMS Manager, where providers and sender IDs live. (There's no "Connect" step — this integration doesn't authenticate against an external service; SMS Manager already holds the credentials.)

> [!NOTE]
> Integration settings can only be edited in an environment with `allowAdminChanges` enabled — the same rule Formie applies to all its integrations. On production, configure them in a dev or staging environment and deploy.

## Turn it on for a form

1. Edit a form and open the **Integrations** tab.
2. Enable your SMS integration. The form-level settings appear:

   ![The SMS integration's form settings](images/sms-notifications-form-settings.webp)

3. Fill in the four fields below, then **Save** the form.

### Sender ID

A single dropdown chooses who the message is sent from. Senders are grouped under their provider, so the routing is visible at a glance — picking a sender tells Formie SMS which provider to dispatch through.

- **Use SMS Manager default (currently: …)** — the first option, shown when SMS Manager has a default sender configured. The form follows whatever the default is at send time, so changing the default in SMS Manager updates this form automatically.
- **A specific sender** — pins the form to that sender regardless of future default changes.

Only enabled sender IDs belonging to enabled providers are listed. Senders marked development-only in SMS Manager show a `[Dev]` suffix. This field is optional — leaving it on the default is a valid choice.

### Recipient(s)

Who to text. Required. Enter one or more numbers, separated by commas, and use Formie variables to pull numbers from the submission:

```
+96512345678
{field:phone}
+96512345678, {field:phone}, {field:altPhone}
```

Each recipient is texted individually. Numbers are normalized and validated before sending — anything that doesn't look like a phone number is skipped (and logged), and if nothing valid remains, the send fails with an error rather than sending to no one. See [Phone number variables](phone-variables.md) for which variable form to use.

### Message

The text to send. Required. Compose it in the rich-text editor and drop in form-field variables to personalize it:

```
Hi {field:name}, we received your request #{field:reference}. We'll be in touch shortly.
```

The message is rendered to plain text before sending — formatting is stripped, variables are replaced with the submitted values. The editor includes left/right alignment so you can compose right-to-left text; when the **Language Filter** is set to Arabic, the editor switches to RTL automatically.

### Language Filter

Controls *which submissions* trigger an SMS, based on the language of the site the form was submitted from. Required.

- **Any Language** — send for every submission (the default).
- **A specific language** — only send when the submission's site language matches. The list is built from your site languages.

This is how you run different SMS per language: add two SMS integrations to the form, set each to a different language, and give each its own message. A submission from a non-matching site is simply skipped — it's expected behavior, not an error.

## How a send works

When a form is submitted, Formie SMS:

1. Confirms SMS Manager is installed and enabled.
2. Checks the **Language Filter** against the submission's site language — and stops here if it doesn't match.
3. Renders the **Recipient(s)** field, splits it on commas, normalizes each number to international format, and drops anything invalid.
4. Resolves the **Sender ID** (a specific sender, or SMS Manager's current default).
5. Renders the **Message** with the submission's data.
6. Hands each recipient + message to SMS Manager to send, under the source name `formie-sms` with the submission ID attached.

SMS Manager records every message in its SMS Logs and rolls it into Analytics. If a send fails, the error is captured against the integration and visible in SMS Manager's logs.

## Limitations

- **SMS Manager does the sending.** If no provider/sender is configured and enabled, or the chosen default doesn't resolve, the send fails with a clear error — Formie SMS never silently substitutes a different sender.
- **One message per recipient.** A comma-separated list sends an individual SMS to each number.
- **Provider rules still apply.** Country allowlists, number formats, and credit are enforced by SMS Manager and the provider — see [Troubleshooting](../resources/troubleshooting.md).

## Next steps

- [Phone number variables](phone-variables.md) — `{field:phone}` vs `{field:phone.number}` and why it matters
- [Troubleshooting](../resources/troubleshooting.md) — when an SMS doesn't arrive
- [Events](../developers/events.md) — modify or cancel a send from your own code
