# Changelog

## [3.11.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.10.0...v3.11.0) (2026-06-07)


### Added

* add siteId to SMS submission payload ([4d5cc49](https://github.com/LindemannRock/craft-formie-sms/commit/4d5cc494dc2acf62b442b5f092e0872e65be9db6))
* add static analysis script for CI workflow ([0f16cb5](https://github.com/LindemannRock/craft-formie-sms/commit/0f16cb5eafa71640def47d6eda075aa5b2ce9656))
* **cli:** add HelpController for cli command assistance ([4ad8789](https://github.com/LindemannRock/craft-formie-sms/commit/4ad8789becb0aad9790af934e601bbec3c7cd06d))
* **settings:** add settings post error handling in setAttributes method ([2670a14](https://github.com/LindemannRock/craft-formie-sms/commit/2670a14ef3074d9662a01a35a756041b8974bace))


### Fixed

* **i18n:** correct SMS failure message punctuation in translations ([ce5549e](https://github.com/LindemannRock/craft-formie-sms/commit/ce5549e10de13e2db1749494b4812ba8d3544d8a))

## [3.10.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.9.0...v3.10.0) - 2026-05-22


### Added

* add pre-commit hook for ECS and PHPStan code quality checks ([444c1d3](https://github.com/LindemannRock/craft-formie-sms/commit/444c1d39882e70faf274a2f091fe75e01c27ea5d))
* **i18n:** add translation issue template for reporting language problems ([ae4a117](https://github.com/LindemannRock/craft-formie-sms/commit/ae4a117fbec556f6b64f865d4ae558755d1a29ac))
* **migrations:** add migration command for legacy senderIdId to senderIdHandle ([71336b2](https://github.com/LindemannRock/craft-formie-sms/commit/71336b24226cbd32fc57ebe533c6ece60d611b47))
* **sms:** enhance sender ID handling and dropdown options ([dd6654a](https://github.com/LindemannRock/craft-formie-sms/commit/dd6654ac90e32f6c40321dc969665f24f4ce606d))
* **sms:** implement sender ID handle resolution and improve recipient validation ([d0fa686](https://github.com/LindemannRock/craft-formie-sms/commit/d0fa686b5f10d414e349a8453a3c62568b30c06f))
* **tests:** add integration tests for SMS recipient parsing and sender ID resolution ([e68803c](https://github.com/LindemannRock/craft-formie-sms/commit/e68803cd818ce86e9c72a904b6121dde3537b9df))


### Fixed

* replace relative path with rootDir in phpstan configuration ([f31788f](https://github.com/LindemannRock/craft-formie-sms/commit/f31788fc08090cbc53fed001c70591c6ba8ce6e2))

## [3.9.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.8.0...v3.9.0) - 2026-05-06


### Features

* add issue templates for bug reports, feature requests, and questions ([bd0234c](https://github.com/LindemannRock/craft-formie-sms/commit/bd0234cd8a9e1b7f63827acd66f97db5e480a561))
* enhance plugin installation experience with configuration guidance ([5339636](https://github.com/LindemannRock/craft-formie-sms/commit/5339636bc37d7dde352f0871b13e60cdfdd74d3e))
* **integration:** enhance recipient parsing for SMS notifications ([8da6f46](https://github.com/LindemannRock/craft-formie-sms/commit/8da6f460be4e303d3addab33c25bed841743ba75))
* **translations:** add new configuration messages for multiple languages ([6d9da07](https://github.com/LindemannRock/craft-formie-sms/commit/6d9da07a03361bfcc3af56f17f1cbcc9334f0a8d))
* **translations:** add translation files for multiple languages ([75bd5d7](https://github.com/LindemannRock/craft-formie-sms/commit/75bd5d7cbee6259e8b2222da64612efc9a7bd808))


### Bug Fixes

* correct version number in Formie SMS config template ([f5a1141](https://github.com/LindemannRock/craft-formie-sms/commit/f5a11415ef2145284d0d4ac167fa629c2b040f4c))
* drop PAT requirement for release-please — use built-in GITHUB_TOKEN ([9acfa97](https://github.com/LindemannRock/craft-formie-sms/commit/9acfa97684be233f96a1413928843a8d35df2639))
* show config override state for plugin name setting ([071b95a](https://github.com/LindemannRock/craft-formie-sms/commit/071b95a1583e12041879a5fde99123aa8331209d))
* update version number in SmsManagerIntegration to 3.1.0 ([c6e9901](https://github.com/LindemannRock/craft-formie-sms/commit/c6e99014b5699f05db71048fe56b06cbc413262b))

## [3.8.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.7.2...v3.8.0) - 2026-04-02


### Features

* **icon:** replace existing icon with new SVG design ([9f964fd](https://github.com/LindemannRock/craft-formie-sms/commit/9f964fd1d9a9cdec2e2ab526cc0d8e29f8c15bf7))

## [3.7.2](https://github.com/LindemannRock/craft-formie-sms/compare/v3.7.1...v3.7.2) - 2026-03-04


### Bug Fixes

* **settings:** add error summary display for plugin settings ([aeb67c4](https://github.com/LindemannRock/craft-formie-sms/commit/aeb67c471eb0038d289a2ef052db4817c3c80349))

## [3.7.1](https://github.com/LindemannRock/craft-formie-sms/compare/v3.7.0...v3.7.1) - 2026-02-22


### Miscellaneous Chores

* add .gitattributes with export-ignore for Packagist distribution ([7934e25](https://github.com/LindemannRock/craft-formie-sms/commit/7934e250f284bb391c888cefccf2aa46be53bd60))
* switch to Craft License for commercial release ([f02e34a](https://github.com/LindemannRock/craft-formie-sms/commit/f02e34abbec67992c2c2fe3ae6dc7cc8af3294cb))

## [3.7.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.6.2...v3.7.0) - 2026-01-26


### Features

* simplify SMS Manager plugin name retrieval and installation checks ([cd68365](https://github.com/LindemannRock/craft-formie-sms/commit/cd683654d8d7044d2f9995544179aa99e68fa3a3))

## [3.6.2](https://github.com/LindemannRock/craft-formie-sms/compare/v3.6.1...v3.6.2) - 2026-01-20


### Bug Fixes

* remove migration for formie-mpp-sms - no longer needed ([28ae475](https://github.com/LindemannRock/craft-formie-sms/commit/28ae4758cd0f7465c4477d9bdd5d42c685842966))

## [3.6.1](https://github.com/LindemannRock/craft-formie-sms/compare/v3.6.0...v3.6.1) - 2026-01-13


### Bug Fixes

* remove unnecessary logging from sendPayload and parsePhoneFieldVariables methods ([e4ef181](https://github.com/LindemannRock/craft-formie-sms/commit/e4ef181eb439b3d701c78b8bf037f8459792c0ab))

## [3.6.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.5.0...v3.6.0) - 2026-01-13


### Features

* update phone field variable pattern to support different separators ([e0ec142](https://github.com/LindemannRock/craft-formie-sms/commit/e0ec1424140ac3213505d88e2359a7bab689360f))

## [3.5.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.4.0...v3.5.0) - 2026-01-13


### Features

* add logging to parsePhoneFieldVariables for improved debugging ([bdbaae4](https://github.com/LindemannRock/craft-formie-sms/commit/bdbaae499451efd89cc0e2ef17e48654e020ac61))

## [3.4.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.3.0...v3.4.0) - 2026-01-13


### Features

* add logging to sendPayload method for better debugging ([7e6b760](https://github.com/LindemannRock/craft-formie-sms/commit/7e6b760f8f1dbef166e64c9e4848626033428f12))

## [3.3.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.2.1...v3.3.0) - 2026-01-13


### Features

* add support for parsing phone field variables in SMS message rendering ([6b11d7d](https://github.com/LindemannRock/craft-formie-sms/commit/6b11d7d4fe601b71c1d30a7b06de7927780ab4ba))

## [3.2.1](https://github.com/LindemannRock/craft-formie-sms/compare/v3.2.0...v3.2.1) - 2026-01-13


### Bug Fixes

* update copyright year to 2026 in multiple files ([7fedf2d](https://github.com/LindemannRock/craft-formie-sms/commit/7fedf2d73d69a26c99d05bb71f88e4f56d89b85a))

## [3.2.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.1.0...v3.2.0) - 2026-01-12


### Features

* **migration:** update MPP-SMS provider settings and add default flag ([2e75d79](https://github.com/LindemannRock/craft-formie-sms/commit/2e75d79c3ca0174bc8cb7f67597a968102feb459))

## [3.1.0](https://github.com/LindemannRock/craft-formie-sms/compare/v3.0.0...v3.1.0) - 2026-01-12


### Features

* add SMS Manager integration and enhance SMS notification handling ([7cc9b8b](https://github.com/LindemannRock/craft-formie-sms/commit/7cc9b8ba7b51fc7016f782a3f1fea1af885ebaf9))

## 3.0.0 - 2026-01-12


### Features

* initial Formie SMS plugin implementation ([8b4bc0e](https://github.com/LindemannRock/craft-formie-sms/commit/8b4bc0ecb35864e36b32205fad9e01c7c390053c))
