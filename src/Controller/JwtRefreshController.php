<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JwtRefreshController
{
    public function __construct(
        private readonly Security $security,
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $user = $this->security->getUser();

        if (null === $user) {
            throw new AccessDeniedException();
        }

        return new JsonResponse(['token' => $this->jwtManager->create($user)]);
    }
}
