<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Functional;

use Galcvua\JwtRefreshBundle\Tests\Application\Kernel;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Panther\PantherTestCase;

abstract class WebTestCase extends PantherTestCase
{
    private const ROUTER_SCRIPT = __DIR__.'/../Application/public/router.php';

    protected static ?string $webServerDir = __DIR__.'/../Application/public';

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected static function createHttpBrowser(): HttpBrowser
    {
        return static::createHttpBrowserClient(self::getPantherServerOptions());
    }

    /**
     * @return array{webServerDir: string|null, router: string, env: array{APP_ENV: string, APP_DEBUG: string}}
     */
    private static function getPantherServerOptions(): array
    {
        return [
            'webServerDir' => self::$webServerDir,
            'router' => self::ROUTER_SCRIPT,
            'env' => [
                'APP_ENV' => 'test',
                'APP_DEBUG' => '1',
            ],
        ];
    }
}
