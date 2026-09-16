<?php

/**
 * PHPUnit bootstrap for the formie-sms plugin.
 *
 * Delegates to the shared base-plugin bootstrap, which initialises Craft as a
 * console application. Tests run against the live DDEV database — there is no
 * transactional rollback. Cleanup is by marker (see `tests/TestCase.php`).
 *
 * @since 3.10.0
 */

declare(strict_types=1);

$baseBootstrap = dirname(__DIR__) . '/vendor/lindemannrock/craft-plugin-base/src/testing/bootstrap.php';
if (!is_file($baseBootstrap)) {
    $baseBootstrap = dirname(__DIR__, 3) . '/vendor/lindemannrock/craft-plugin-base/src/testing/bootstrap.php';
}

if (!file_exists($baseBootstrap)) {
    fwrite(STDERR, "Base plugin testing bootstrap not found at {$baseBootstrap}\n");
    fwrite(STDERR, "Run `composer install` and ensure lindemannrock/craft-plugin-base ^5.0 is present.\n");
    exit(1);
}

require_once $baseBootstrap;

$testProjectRoot = \craft\helpers\App::env('FORMIE_SMS_TEST_PROJECT_ROOT');
\lindemannrock\base\testing\bootstrap(is_string($testProjectRoot) && $testProjectRoot !== '' ? $testProjectRoot : null);
