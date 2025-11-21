<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'secret' => 'MiniAppSecret',
        'handle_all_throwables' => false,
        'http_method_override' => false,
        'test' => true,
        'session' => [
            'storage_factory_id' => 'session.storage.factory.mock_file',
        ],
        'router' => [
            'resource' => '%kernel.project_dir%/config/routes.php',
            'type' => 'php',
            'utf8' => true,
        ],
    ]);
};
