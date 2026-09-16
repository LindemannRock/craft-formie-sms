# Quickstart

Connect a Formie form to SMS Manager so it sends a text when someone submits it — all from the Control Panel, no code.

## Before you start

Complete [Installation & Setup](installation.md#post-install-setup) first. Formie and SMS Manager should be installed and enabled, with an enabled SMS provider and sender ID.

## 1. Make sure SMS Manager can send

Formie SMS sends through SMS Manager, so SMS Manager needs to be ready first. In **SMS Manager**, confirm you have:

- At least one **enabled provider** (e.g. MPP-SMS or Twilio).
- At least one **enabled sender ID** linked to that provider.

If you can send a message from **SMS Manager → Settings → Test SMS**, you're good to go.

## 2. Create the SMS integration in Formie

1. Go to **Formie → Settings → Integrations** and click **New Integration**.
2. Under **Miscellaneous**, choose the SMS Manager integration (it shows your SMS Manager plugin name).
3. Give it a **Name** (e.g. "SMS Notifications"), then save.

## 3. Turn it on for a form

1. Edit a Formie form and open the **Integrations** tab.
2. Enable your SMS integration and fill in:
   - **Sender ID** — pick a sender, or **Use SMS Manager default**.
   - **Recipient(s)** — a phone number or a form field variable like `{field:phone}`.
   - **Message** — your text, with variables like `Hi {field:name}!`.
   - **Language Filter** — leave on **Any Language** to send for every submission.
3. Save the form.

## 4. Verify it works

Submit the form on your site, then open **SMS Manager → SMS Logs**. You should see the message with its recipient, content, and delivery status — its source shown as `formie-sms`.

## What's next

- [SMS notifications](../feature-tour/sms-notifications.md) — every form-level setting explained
- [Phone number variables](../feature-tour/phone-variables.md) — pick the right `{field:phone}` form for reliable delivery
- [Configuration](configuration.md) — rename the plugin per environment
