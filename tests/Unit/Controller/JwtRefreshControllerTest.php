<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Unit\Controller;

use Galcvua\JwtRefreshBundle\Controller\JwtRefreshController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

#[CoversClass(JwtRefreshController::class)]
#[CoversMethod(JwtRefreshController::class, '__invoke')]
final class JwtRefreshControllerTest extends TestCase
{
    public function testReturnsTokenForAuthenticatedUser(): void
    {
        $user = $this->createStub(UserInterface::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $jwtManager->expects($this->once())->method('create')->with($user)->willReturn('jwt-token');

        $controller = new JwtRefreshController($security, $jwtManager);

        $response = $controller();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['token' => 'jwt-token'], json_decode((string) $response->getContent(), true));
    }

    public function testThrowsWhenUserIsMissing(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $jwtManager = $this->createMock(JWTTokenManagerInterface::class);

        $controller = new JwtRefreshController($security, $jwtManager);

        $this->expectException(AccessDeniedException::class);

        $controller();
    }
}
