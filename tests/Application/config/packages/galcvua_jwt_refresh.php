<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('galcvua_jwt_refresh', [
        'refresh_token_firewall' => 'refresh',
        'refresh_token_route' => 'api_token_refresh',
        'open_api' => [
            'enabled' => false,
        ],
    ]);
};
