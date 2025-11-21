<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\OpenApi;

use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use Symfony\Component\HttpFoundation\Response;

final class OpenApiRefreshFactory extends OpenApiAbstractFactory
{
    protected function injectEndPoint(): void
    {
        $this->openApi
            ->getPaths()
            ->addPath($this->path, (new PathItem())->withPost(
                (new Operation())
                    ->withOperationId('token_refresh_post')
                    ->withTags($this->tags)
                    ->withSummary('Refresh a user token')
                    ->withResponses([
                        Response::HTTP_OK => [
                            'description' => 'Fresh user token created',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'token' => [
                                                'readOnly' => true,
                                                'type' => 'string',
                                                'nullable' => false,
                                            ],
                                        ],
                                        'required' => ['token'],
                                    ],
                                ],
                            ],
                        ],
                    ])
            ));
    }
}
