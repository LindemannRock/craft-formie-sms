<?php

/**
 * PHPUnit bootstrap for the formie-sms plugin.
 *
 * Delegates to the shared base-plugin bootstrap, which initialises Craft as a
 * console application. Provider HTTP is denied before plugins boot. Connection-
 * local tables isolate the queue and SMS Manager writes; Formie elements use
 * Base's tracked cleanup (see `tests/TestCase.php`). No queue workers run.
 *
 * @since 3.10.0
 */

declare(strict_types=1);

/** Install transport and persistence isolation before enabled plugins boot. */
function craft_modify_app_config(array &$config, string $appType): void
{
    \lindemannrock\formiesms\tests\Support\TransportIsolation::install();
    if ($appType !== 'console') {
        throw new \RuntimeException('Formie SMS tests require the console application.');
    }
    $config['components']['queue']['class'] = \lindemannrock\formiesms\tests\Support\IsolatedQueue::class;
    $config['components']['queue']['proxyQueue'] = null;
}

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
