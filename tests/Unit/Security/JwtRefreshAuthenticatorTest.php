<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Unit\Security;

use Galcvua\JwtRefreshBundle\Security\JwtRefreshAuthenticator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Service\ServiceCollectionInterface;

#[CoversClass(JwtRefreshAuthenticator::class)]
final class JwtRefreshAuthenticatorTest extends TestCase
{
    public function testSupportsAlwaysReturnsFalse(): void
    {
        $authenticator = new JwtRefreshAuthenticator($this->createStub(ServiceCollectionInterface::class));

        self::assertFalse($authenticator->supports(new Request()));
    }

    public function testAuthenticateThrowsBecauseSupportsIsFalse(): void
    {
        $authenticator = new JwtRefreshAuthenticator($this->createStub(ServiceCollectionInterface::class));

        $this->expectException(\LogicException::class);

        $authenticator->authenticate(new Request());
    }

    public function testOnAuthenticationSuccessStoresSerializedTokenInSession(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);

        $token = $this->createStub(TokenInterface::class);

        $firewallConfig = new FirewallConfig(
            name: 'main',
            userChecker: 'security.user_checker',
            context: 'refresh_context',
        );

        $firewallContext = $this->createMock(FirewallContext::class);
        $firewallContext->method('getConfig')->willReturn($firewallConfig);

        $locator = $this->createMock(ServiceCollectionInterface::class);
        $locator->expects($this->once())
            ->method('get')
            ->with('security.firewall.map.context.main')
            ->willReturn($firewallContext);

        $authenticator = new JwtRefreshAuthenticator($locator);

        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        self::assertNull($response);
        self::assertSame(
            serialize($token),
            $session->get('_security_refresh_context'),
        );
    }

    public function testOnAuthenticationFailureReturnsNull(): void
    {
        $authenticator = new JwtRefreshAuthenticator($this->createStub(ServiceCollectionInterface::class));

        $response = $authenticator->onAuthenticationFailure(new Request(), $this->createStub(AuthenticationException::class));

        self::assertNull($response);
    }
}
