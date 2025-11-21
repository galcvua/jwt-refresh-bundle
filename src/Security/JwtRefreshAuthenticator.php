<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Security;

use Symfony\Bundle\SecurityBundle\Security\FirewallContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Contracts\Service\ServiceCollectionInterface;

final class JwtRefreshAuthenticator extends AbstractAuthenticator
{
    /**
     * @param ServiceCollectionInterface<FirewallContext> $locator
     */
    public function __construct(private ServiceCollectionInterface $locator)
    {
    }

    public function supports(Request $request): ?bool
    {
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        throw new \LogicException('This method should not be called because "supports()" always returns false.');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $session = $request->getSession();

        $firewallContext = $this->locator->get(sprintf('security.firewall.map.context.%s', $firewallName));
        $firewallConfig = $firewallContext->getConfig();

        if (null === $firewallConfig) {
            throw new \LogicException(sprintf('No firewall configuration found for firewall "%s".', $firewallName));
        }

        $sessionKey = sprintf('_security_%s', $firewallConfig->getContext() ?? $firewallName);

        $session->set($sessionKey, serialize($token));

        $session->save();

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
