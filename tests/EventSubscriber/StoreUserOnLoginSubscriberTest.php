<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\EventSubscriber;

use Galcvua\JwtRefreshBundle\EventSubscriber\StoreUserOnLoginSubscriber;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

#[CoversClass(StoreUserOnLoginSubscriber::class)]
#[CoversMethod(StoreUserOnLoginSubscriber::class, 'onAuthenticationSuccess')]
final class StoreUserOnLoginSubscriberTest extends TestCase
{
    public function testDoesNothingWhenRequestIsMissing(): void
    {
        $security = $this->createMock(Security::class);
        $security->expects($this->never())->method('login');

        $subscriber = new StoreUserOnLoginSubscriber(
            $security,
            'refresh',
            new RequestStack(),
        );

        $event = new AuthenticationSuccessEvent([], $this->createStub(UserInterface::class), new Response());

        $subscriber->onAuthenticationSuccess($event);
    }

    public function testSkipsLoginForRefreshFirewall(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $security = $this->createMock(Security::class);
        $security->expects($this->once())
            ->method('getFirewallConfig')
            ->willReturn(new FirewallConfig('refresh', 'security.user_checker'));
        $security->expects($this->never())->method('login');

        $subscriber = new StoreUserOnLoginSubscriber(
            $security,
            'refresh',
            $requestStack,
        );

        $event = new AuthenticationSuccessEvent([], $this->createStub(UserInterface::class), new Response());

        $subscriber->onAuthenticationSuccess($event);
    }

    public function testLogsUserIntoRefreshFirewallWhenDifferentFirewall(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $user = $this->createStub(UserInterface::class);

        $security = $this->createMock(Security::class);
        $security->expects($this->once())
            ->method('getFirewallConfig')
            ->willReturn(new FirewallConfig('main', 'security.user_checker'));

        $security->expects($this->once())
            ->method('login')
            ->with($user, null, 'refresh');

        $subscriber = new StoreUserOnLoginSubscriber(
            $security,
            'refresh',
            $requestStack,
        );

        $event = new AuthenticationSuccessEvent([], $user, new Response());

        $subscriber->onAuthenticationSuccess($event);
    }
}
