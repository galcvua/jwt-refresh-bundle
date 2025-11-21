<?php

declare(strict_types=1);

use Galcvua\JwtRefreshBundle\Tests\Application\Controller\MeController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->add('api_token_refresh', '/token/refresh')
        ->controller('galcvua_jwt_refresh.controller.jwt_refresh')
        ->methods(['POST']);

    $routes
        ->add('security_login', '/login')
        ->methods(['POST']);

    $routes
        ->add('app_logout', '/logout')
        ->methods(['POST']);

    $routes
        ->add('api_me', '/api/me')
        ->controller(MeController::class)
        ->methods(['GET']);
};
