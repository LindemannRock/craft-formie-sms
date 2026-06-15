# Phone number variables

Pull a submitter's phone number into the **Recipient(s)** field with a Formie variable — and pick the right form of it so the number actually dispatches. The short version: use `{field:phone}`, not `{field:phone.number}`.

This page explains why, and what the phone sub-variables are good for.

## The two forms of a phone field

A Formie Phone field holds more than a string — it knows the country code, the local number, and the country. When you reference it, *how* you reference it changes what you get:

| Variable | Produces | Example |
|----------|----------|---------|
| `{field:phone}` | The full number in international format | `+965 9725 5330` |
| `{field:phone.number}` | Just the local digits, no country code | `9725 5330` |

> [!TIP]
> For the **Recipient(s)** field, always use `{field:phone}`. It carries the country code, which is what makes a number deliverable across every provider.

## Why `{field:phone}` is the portable choice

Before sending, Formie SMS normalizes each recipient to **E.164** — the international standard like `+96597255330`. `{field:phone}` renders with the country code, so it collapses cleanly to E.164 and works with any provider, including strict international gateways like Twilio.

`{field:phone.number}` has no country code. It will only be delivered if the provider can infer and prepend one — which SMS Manager does only for the GCC/MENA countries its providers support. Send those bare digits to a strict international provider and the number is rejected. Unless you have a specific reason to strip the country code, don't.

This is exactly what the help text under the Recipient(s) field tells you, and why the field defaults to encouraging `{field:phone}`.

## Phone sub-variables (for the message body)

The other parts of a Phone field are available as sub-variables. These are most useful inside the **Message**, not as recipients:

| Variable | Returns |
|----------|---------|
| `{field:phone.countryCode}` | The dialling country code (e.g. `+965`) |
| `{field:phone.number}` | The local number without the country code |
| `{field:phone.country}` | The two-letter country (e.g. `KW`) |
| `{field:phone.countryName}` | The country name (e.g. `Kuwait`) |

```
We'll call you back on {field:phone.countryCode} {field:phone.number}.
```

> [!NOTE]
> Depending on your Formie version, the variable picker may insert either a colon or a dot separator (`{field:phone}` or `{field.phone}`). Both are handled — use whatever the picker gives you.

![The Recipient(s) field with a phone variable](images/phone-variables-recipients.webp)

## Next steps

- [SMS notifications](sms-notifications.md) — the form-level settings these variables go in
- [Troubleshooting](../resources/troubleshooting.md) — when a recipient number is skipped or rejected
