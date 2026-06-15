# Troubleshooting

Common issues and how to resolve them. Most send problems show up in **SMS Manager → SMS Logs** (per-message provider responses) — check there first, then your `storage/logs` for plugin-level detail.

## The SMS integration doesn't appear in Formie

1. **Both plugins installed and enabled?** Formie SMS needs [Formie](https://verbb.io/craft-plugins/formie) *and* [SMS Manager](https://github.com/LindemannRock/craft-sms-manager) installed and enabled under **Settings → Plugins**.
2. **Clear caches.** `php craft clear-caches/all` (or `ddev craft clear-caches/all`).
3. Look under **Formie → Settings → Integrations → New Integration → Miscellaneous** — it's listed with your SMS Manager plugin name.

## No senders in the Sender ID dropdown

The dropdown only lists **enabled** sender IDs that belong to **enabled** providers. If it's empty:

1. In SMS Manager, confirm at least one provider is enabled.
2. Confirm at least one sender ID is enabled and linked to that provider.
3. A sender under a disabled provider won't appear — enable the provider too.

## SMS isn't sending

Work through these in order:

1. **Language Filter.** If it's set to a specific language, the form must be submitted from a site with that language. Set it to **Any Language** to send for every submission.
2. **Recipients.** Make sure the number(s) are valid and that any variable (like `{field:phone}`) actually resolves on the submission. Invalid tokens are skipped.
3. **Sender resolves.** Either pick a specific sender, or make sure SMS Manager has a **default sender** configured if you chose "Use SMS Manager default".
4. **Check the SMS log.** Each failed message stores the provider's error — that usually names the cause.
5. **Provider status and credit.** Confirm the provider is enabled and the account has balance.

## Error: "No valid recipients after rendering"

The Recipient(s) field rendered to nothing usable — every value was empty or failed phone validation.

- Check the variable handle matches a real field (e.g. `{field:phone}`, case-sensitive).
- Prefer `{field:phone}` over `{field:phone.number}` — the bare local number has no country code and may be rejected. See [Phone number variables](../feature-tour/phone-variables.md).
- Test with a static number like `+96512345678` first to isolate the issue.

## Error: "No sender ID configured for this integration"

The send couldn't resolve a sender. This is intentional — Formie SMS won't silently pick a different one.

- Edit the form's SMS integration and pick a specific **Sender ID**, **or**
- Choose **Use SMS Manager default** and set a default sender in SMS Manager.

## The message arrives empty

- Field handles are case-sensitive — `{field:firstName}` ≠ `{field:firstname}`. Use the variable picker in the editor to insert them correctly.
- Test with a plain static message first to confirm the path works, then add variables back.

## Integration settings are greyed out / can't be saved

Formie only allows integration settings to be edited where `allowAdminChanges` is enabled. On production this is typically off — configure the integration in a dev or staging environment and deploy the change.

## Upgraded from an older version and a form's sender is wrong

Older Formie SMS versions stored sender IDs by numeric database ID. Run the migration to populate the current handle-based reference:

```bash title="PHP"
php craft formie-sms/migrate/integration-handles
```

```bash title="DDEV"
ddev craft formie-sms/migrate/integration-handles
```

A form reported as **Unresolved** points at a sender that no longer exists — re-pick a sender on its Integrations tab. See [Console Commands](../developers/console-commands.md).
