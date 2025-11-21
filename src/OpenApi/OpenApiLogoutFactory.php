<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\OpenApi;

use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use Symfony\Component\HttpFoundation\Response;

final class OpenApiLogoutFactory extends OpenApiAbstractFactory
{
    protected function injectEndPoint(): void
    {
        $this->openApi
            ->getPaths()
            ->addPath($this->path, (new PathItem())->withPost(
                (new Operation())
                    ->withOperationId('token_logout_post')
                    ->withTags($this->tags)
                    ->withSummary('Logout user from refresh token firewall')
                    ->withResponses([
                        Response::HTTP_NO_CONTENT => [
                            'description' => 'User logged out successfully',
                            'content' => [
                                'application/json' => [
                                ],
                            ],
                        ],
                    ])
            ));
    }
}
