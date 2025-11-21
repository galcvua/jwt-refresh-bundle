<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class OpenApiAbstractFactory implements OpenApiFactoryInterface
{
    protected OpenApi $openApi;
    protected string $path;

    /**
     * @param string[] $tags
     */
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $routeName,
        protected readonly array $tags,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $this->openApi = ($this->decorated)($context);

        try {
            $this->path = $this->urlGenerator->generate($this->routeName);
        } catch (RouteNotFoundException) {
            return $this->openApi;
        }

        $this->injectEndPoint();

        return $this->openApi;
    }

    abstract protected function injectEndPoint(): void;
}
