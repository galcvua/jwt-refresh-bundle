<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class StoreUserOnLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly string $refreshTokenFirewall,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $firewallConfig = $this->security->getFirewallConfig($request);

        if ($firewallConfig?->getName() === $this->refreshTokenFirewall) {
            return;
        }

        $this->security->login(
            firewallName: $this->refreshTokenFirewall,
            user: $user,
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}
