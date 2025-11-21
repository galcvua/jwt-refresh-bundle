<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Functional;

use Galcvua\JwtRefreshBundle\Tests\Application\Kernel;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ApplicationKernelTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    #[RunInSeparateProcess]
    public function testKernelBoots(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        self::assertTrue($container->has('security.helper'));
        self::assertTrue($container->has('lexik_jwt_authentication.jwt_manager'));
        self::assertTrue($container->has('galcvua_jwt_refresh.controller.jwt_refresh'));
    }
}
