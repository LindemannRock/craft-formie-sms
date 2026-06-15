# Console Commands

Formie SMS ships a small console toolkit, mostly for upgrading older form data to the current sender-handle format.

## `formie-sms/help`

Lists the available Formie SMS commands with focused guidance, including examples and notes for each.

```bash title="PHP"
php craft formie-sms/help
```

```bash title="DDEV"
ddev craft formie-sms/help
```

For guidance on a single command, pass its path:

```bash title="PHP"
php craft formie-sms/help migrate/integration-handles
```

```bash title="DDEV"
ddev craft formie-sms/help migrate/integration-handles
```

Craft's native help still works when you want the exact Yii option signature: `php craft help formie-sms/migrate/integration-handles`.

## `formie-sms/migrate/integration-handles`

Upgrades Formie SMS integration data from the old numeric sender reference to the current handle-based one.

```bash title="PHP"
php craft formie-sms/migrate/integration-handles
```

```bash title="DDEV"
ddev craft formie-sms/migrate/integration-handles
```

**When to run it:** after upgrading from an older Formie SMS version that stored SMS Manager sender IDs by their numeric database ID. The command scans Formie form settings for the legacy `senderIdId` value and writes the matching `senderIdHandle` when the sender still exists in SMS Manager. Forms saved on a current version already store the handle and need nothing.

**What it reports:** per form, one of *Migrated*, *Already current*, *Unresolved*, or *Errored*, followed by a summary count.

> [!NOTE]
> The command is idempotent — safe to run repeatedly. It updates Formie's form settings directly without triggering Formie's save hooks, and leaves the legacy `senderIdId` / `providerId` values in place for rollback safety. A form whose `senderIdId` points at a deleted sender is reported as **Unresolved**; fix it by re-picking a sender on the form's Integrations tab.
