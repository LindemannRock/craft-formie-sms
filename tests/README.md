# Formie SMS tests

Package tests extend `tests/TestCase.php` and Base's tracked Craft test lifecycle. Parsing/sender-resolution tests use service stubs; `SmsTransportHoldTest` exercises actual SMS Manager dispatch, classification and persistence with synthetic provider responses.

`SmsSenderHoldNoticeTest` uses `StatusProvider` and `StatusTransport` to supply provider-owned lane and safe status projections. It verifies timed/manual holds, independent development routing, expired/unavailable status, the missing-status-capability fallback and the rendered inline same-tab link. These status-only tests make no provider requests.

`tests/bootstrap.php` installs `TransportIsolation` before plugins boot. Guzzle has only an explicit MockHandler: an unexpected request without one exits with code 90 before network dispatch. Each process reports intercepted request count, real requests (zero) and workers executed (zero). Synthetic dispatch uses `gateway.example.test`, example phone numbers and synthetic credentials.

`IsolatedQueue` creates MySQL connection-local temporary shadows before plugin bootstrap: queue, SMS logs/analytics and transport guards/releases start empty; settings, providers and sender IDs start as copied rows. Tests write only those shadows. MySQL removes them when the connection closes, including after failure. Queue execution exits with code 91. Do not run queue workers for this suite.

Provider/sender fixtures and Formie forms/submissions are owned by the tracked test lifecycle. Test-specific component/configuration, settings, logger and locale/site changes are restored. The hold test clears only connection-owned shadows between cases. The Formie job test directly invokes one synthetic job to observe its false-result exception, then inspects retry policy without executing a retry.

From the development workspace:

```bash
ddev exec "cd plugins/formie-sms && composer test -- --filter SmsTransportHoldTest"
ddev exec "cd plugins/formie-sms && composer ci"
ddev exec "cd plugins/formie-sms && composer quality-gate"
```

From the plugin directory, `./scripts/act-quality-gates` runs the same package gate in its isolated project. The runner owns and removes its project, service and network resources. General testing and gate policy remains in the repository workflow; this file documents only this package's fixtures and safety boundaries.
