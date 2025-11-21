<?php

declare(strict_types=1);

use Galcvua\JwtRefreshBundle\Tests\Application\Controller\MeController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services
        ->set(MeController::class)
        ->public()
        ->arg('$security', service('security.helper'));
};
