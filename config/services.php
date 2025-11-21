<?php

declare(strict_types=1);

use Galcvua\JwtRefreshBundle\Controller\JwtRefreshController;
use Galcvua\JwtRefreshBundle\EventSubscriber\LogoutSubscriber;
use Galcvua\JwtRefreshBundle\EventSubscriber\StoreUserOnLoginSubscriber;
use Galcvua\JwtRefreshBundle\Security\JwtRefreshAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->set('galcvua_jwt_refresh.subscriber.store_user_on_login', StoreUserOnLoginSubscriber::class)
        ->args([
            '$security' => service('security.helper'),
            '$refreshTokenFirewall' => '%galcvua_jwt_refresh.refresh_token_firewall%',
            '$requestStack' => service('request_stack'),
        ])
        ->tag('kernel.event_subscriber');

    $services
        ->set('galcvua_jwt_refresh.subscriber.logout', LogoutSubscriber::class)
        ->tag('kernel.event_subscriber');

    $services
        ->set('galcvua_jwt_refresh.security.jwt_refresh_authenticator', JwtRefreshAuthenticator::class)
        ->arg('$locator', service('security.firewall.context_locator'));

    $services
        ->set('galcvua_jwt_refresh.controller.jwt_refresh', JwtRefreshController::class)
        ->public()
        ->args([
            '$security' => service('security.helper'),
            '$jwtManager' => service('lexik_jwt_authentication.jwt_manager'),
        ]);
};
