<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle;

use Galcvua\JwtRefreshBundle\OpenApi\OpenApiLogoutFactory;
use Galcvua\JwtRefreshBundle\OpenApi\OpenApiRefreshFactory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class GalcvuaJwtRefreshBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $defaultOpenApiTags = ['JWT Refresh'];

        // @phpstan-ignore method.notFound
        $definition->rootNode()
            ->children()
                ->scalarNode('refresh_token_firewall')->defaultValue('refresh_token')->end()
                ->scalarNode('refresh_token_route')->defaultValue('api_token_refresh')->end()
                ->arrayNode('open_api')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('refresh')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->arrayNode('tags')
                                    ->stringPrototype()->end()
                                    ->defaultValue($defaultOpenApiTags)
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('logout')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->arrayNode('tags')
                                    ->stringPrototype()->end()
                                    ->defaultValue($defaultOpenApiTags)
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('galcvua_jwt_refresh.refresh_token_firewall', $config['refresh_token_firewall']);
        $builder->setParameter('galcvua_jwt_refresh.refresh_token_route', $config['refresh_token_route']);

        $container->import('../config/services.php');

        $services = $container->services();

        $services->get('galcvua_jwt_refresh.subscriber.logout')
            ->tag('kernel.event_subscriber', [
                'dispatcher' => 'security.event_dispatcher.'.$config['refresh_token_firewall'],
            ])
        ;

        $refreshDecoratorId = 'galcvua_jwt_refresh.openapi.refresh_factory';

        if ($config['open_api']['refresh']['enabled'] ?? true) {
            $services
                ->set($refreshDecoratorId, OpenApiRefreshFactory::class)
                ->decorate(
                    id: 'api_platform.openapi.factory',
                    invalidBehavior: ContainerInterface::IGNORE_ON_INVALID_REFERENCE
                )
                ->args([
                    new Reference($refreshDecoratorId.'.inner'),
                    new Reference('router.default'),
                    $config['refresh_token_route'],
                    $config['open_api']['refresh']['tags'],
                ])
            ;
        }

        if ($config['open_api']['logout']['enabled'] ?? true) {
            $logoutDecoratorId = 'galcvua_jwt_refresh.openapi.logout_factory';

            $services
                ->set($logoutDecoratorId, OpenApiLogoutFactory::class)
                ->decorate(
                    id: 'api_platform.openapi.factory',
                    invalidBehavior: ContainerInterface::IGNORE_ON_INVALID_REFERENCE
                )
                ->args([
                    new Reference($logoutDecoratorId.'.inner'),
                    new Reference('router.default'),
                    sprintf('_logout_%s', $config['refresh_token_firewall']),
                    $config['open_api']['logout']['tags'],
                ])
            ;
        }
    }
}
