<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => ['onLogout', 63]];
    }

    public function onLogout(LogoutEvent $event): void
    {
        if (null === $event->getToken()?->getUser()) {
            throw new AccessDeniedException();
        }

        $event->setResponse(new Response(status: Response::HTTP_NO_CONTENT));
    }
}
