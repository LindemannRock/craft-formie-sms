<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Support;

use Craft;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;

/**
 * Fail closed before any provider HTTP request can leave the test process.
 * @since 3.11.3
 */
final class TransportIsolation
{
    public static ?MockHandler $handler = null;
    public static int $interceptedRequests = 0;

    public static function install(): void
    {
        Craft::$container->set(Client::class, static function($container, array $params): Client {
            $config = $params[0] ?? [];
            $config['handler'] = static function($request, array $options) {
                if (self::$handler === null) {
                    // Providers catch Throwable, so an exception is not a safe stop.
                    fwrite(STDERR, "Forbidden network fallback; no request was made.\n");
                    exit(90);
                }
                self::$interceptedRequests++;
                return (self::$handler)($request, $options);
            };
            return new Client($config);
        });
        register_shutdown_function(static function(): void {
            fwrite(STDERR, 'Formie SMS transport: intercepted=' . self::$interceptedRequests . "; real=0; queue-workers=0\n");
        });
    }
}
