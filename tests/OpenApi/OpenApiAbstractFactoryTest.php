<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;
use Galcvua\JwtRefreshBundle\OpenApi\OpenApiAbstractFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(OpenApiAbstractFactory::class)]
#[CoversMethod(OpenApiAbstractFactory::class, '__invoke')]
final class OpenApiAbstractFactoryTest extends TestCase
{
    public function testInvokesDecoratorAndInjectsEndpoint(): void
    {
        $factory = new class($this->createDecoratedFactory(), $this->createUrlGenerator('/path'), 'route_name', []) extends OpenApiAbstractFactory {
            public int $injected = 0;

            protected function injectEndPoint(): void
            {
                ++$this->injected;
            }
        };

        $openApi = $factory();

        self::assertInstanceOf(OpenApi::class, $openApi);
        self::assertSame(1, $factory->injected);
    }

    public function testSkipsWhenRouteMissing(): void
    {
        $factory = new class($this->createDecoratedFactory(), $this->createThrowingUrlGenerator(), 'route_name', []) extends OpenApiAbstractFactory {
            public int $injected = 0;

            protected function injectEndPoint(): void
            {
                ++$this->injected;
            }
        };

        $openApi = $factory();

        self::assertInstanceOf(OpenApi::class, $openApi);
        self::assertSame(0, $factory->injected);
    }

    private function createDecoratedFactory(): OpenApiFactoryInterface
    {
        return new class implements OpenApiFactoryInterface {
            public function __invoke(array $context = []): OpenApi
            {
                return new OpenApi(new Info('Test', '1.0.0'), [], new Paths());
            }
        };
    }

    private function createUrlGenerator(string $resolvedPath): UrlGeneratorInterface
    {
        $generator = $this->createMock(UrlGeneratorInterface::class);
        $generator->method('generate')->willReturn($resolvedPath);

        return $generator;
    }

    private function createThrowingUrlGenerator(): UrlGeneratorInterface
    {
        $generator = $this->createMock(UrlGeneratorInterface::class);
        $generator->method('generate')->willThrowException(new RouteNotFoundException());

        return $generator;
    }
}
