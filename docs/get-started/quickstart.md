# Quickstart

Get Formie SMS running in under 5 minutes. By the end you'll have a Formie form that sends an SMS through SMS Manager every time it's submitted — all from the Control Panel, no code.

## 1. Install the plugin

See [Installation](installation.md) for full details.

```bash title="Composer"
composer require lindemannrock/craft-formie-sms && php craft plugin/install formie-sms
```

```bash title="DDEV"
ddev composer require lindemannrock/craft-formie-sms && ddev craft plugin/install formie-sms
```

## 2. Make sure SMS Manager can send

Formie SMS sends through SMS Manager, so SMS Manager needs to be ready first. In **SMS Manager**, confirm you have:

- At least one **enabled provider** (e.g. MPP-SMS or Twilio).
- At least one **enabled sender ID** linked to that provider.

If you can send a message from **SMS Manager → Settings → Test SMS**, you're good to go.

## 3. Create the SMS integration in Formie

1. Go to **Formie → Settings → Integrations** and click **New Integration**.
2. Under **Miscellaneous**, choose the SMS Manager integration (it shows your SMS Manager plugin name).
3. Give it a **Name** (e.g. "SMS Notifications"), then save.

## 4. Turn it on for a form

1. Edit a Formie form and open the **Integrations** tab.
2. Enable your SMS integration and fill in:
   - **Sender ID** — pick a sender, or **Use SMS Manager default**.
   - **Recipient(s)** — a phone number or a form field variable like `{field:phone}`.
   - **Message** — your text, with variables like `Hi {field:name}!`.
   - **Language Filter** — leave on **Any Language** to send for every submission.
3. Save the form.

## 5. Verify it works

Submit the form on your site, then open **SMS Manager → SMS Logs**. You should see the message with its recipient, content, and delivery status — its source shown as `formie-sms`.

## What's next

- [SMS notifications](../feature-tour/sms-notifications.md) — every form-level setting explained
- [Phone number variables](../feature-tour/phone-variables.md) — pick the right `{field:phone}` form for reliable delivery
- [Configuration](configuration.md) — rename the plugin per environment
