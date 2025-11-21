<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Functional;

use Galcvua\JwtRefreshBundle\Tests\Application\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
