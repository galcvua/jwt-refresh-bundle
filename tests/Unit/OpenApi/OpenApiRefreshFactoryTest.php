<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Unit\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;
use Galcvua\JwtRefreshBundle\OpenApi\OpenApiRefreshFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(OpenApiRefreshFactory::class)]
#[CoversMethod(OpenApiRefreshFactory::class, 'injectEndPoint')]
final class OpenApiRefreshFactoryTest extends TestCase
{
    public function testInjectsRefreshEndpoint(): void
    {
        $refreshPath = '/token/refresh';

        $factory = new OpenApiRefreshFactory(
            $this->createDecoratedFactory(),
            $this->createUrlGenerator($refreshPath),
            'refresh_route',
            ['JWT Refresh'],
        );

        $openApi = new OpenApi(new Info('Test', '1.0.0'), [], new Paths());

        $ref = new \ReflectionClass($factory);
        $pathProp = $ref->getProperty('path');
        $pathProp->setAccessible(true);
        $pathProp->setValue($factory, $refreshPath);

        $openApiProp = $ref->getProperty('openApi');
        $openApiProp->setAccessible(true);
        $openApiProp->setValue($factory, $openApi);

        $method = $ref->getMethod('injectEndPoint');
        $method->setAccessible(true);
        $method->invoke($factory);

        $pathItem = $openApi->getPaths()->getPath($refreshPath);

        self::assertNotNull($pathItem);
        self::assertSame('token_refresh_post', $pathItem->getPost()?->getOperationId());
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
}
