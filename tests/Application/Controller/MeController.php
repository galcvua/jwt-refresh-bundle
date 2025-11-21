<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Application\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class MeController
{
    public function __construct(private readonly Security $security)
    {
    }

    public function __invoke(): Response
    {
        $user = $this->security->getUser();

        return new JsonResponse([
            'user' => $user?->getUserIdentifier(),
        ]);
    }
}
