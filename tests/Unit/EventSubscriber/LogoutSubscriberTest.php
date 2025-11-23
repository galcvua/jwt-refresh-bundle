<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Unit\EventSubscriber;

use Galcvua\JwtRefreshBundle\EventSubscriber\LogoutSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[CoversClass(LogoutSubscriber::class)]
#[CoversMethod(LogoutSubscriber::class, 'onLogout')]
final class LogoutSubscriberTest extends TestCase
{
    public function testSetsNoContentResponseWhenUserExists(): void
    {
        $user = $this->createStub(UserInterface::class);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $event = new LogoutEvent(new Request(), $token);

        $subscriber = new LogoutSubscriber();
        $subscriber->onLogout($event);

        $response = $event->getResponse();

        self::assertNotNull($response);
        self::assertSame(204, $response->getStatusCode());
    }

    public function testThrowsWhenUserIsMissing(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $event = new LogoutEvent(new Request(), $token);

        $subscriber = new LogoutSubscriber();

        $this->expectException(AccessDeniedException::class);

        $subscriber->onLogout($event);
    }
}
