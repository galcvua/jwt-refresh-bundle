<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('security', [
        'password_hashers' => [
            'Symfony\\Component\\Security\\Core\\User\\InMemoryUser' => 'plaintext',
        ],
        'providers' => [
            'app' => [
                'memory' => [
                    'users' => [
                        'user@example.com' => [
                            'password' => 'password',
                            'roles' => ['ROLE_USER'],
                        ],
                    ],
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'login' => [
                'pattern' => '^/login',
                'stateless' => true,
                'provider' => 'app',
                'json_login' => [
                    'check_path' => '/login',
                    'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                    'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                ],
            ],
            'refresh' => [
                'pattern' => '^/(token/refresh|logout)',
                'provider' => 'app',
                'custom_authenticators' => ['galcvua_jwt_refresh.security.jwt_refresh_authenticator'],
                'logout' => [
                    'path' => '/logout',
                ],
            ],
            'api' => [
                'pattern' => '^/api',
                'stateless' => true,
                'provider' => 'app',
                'jwt' => null,
            ],
        ],

        'access_control' => [
            ['path' => '^/login', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/token/refresh', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/logout', 'roles' => 'IS_AUTHENTICATED_FULLY'],
            ['path' => '^/api', 'roles' => 'IS_AUTHENTICATED_FULLY'],
        ],
    ]);
};
