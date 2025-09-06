<?php

declare(strict_types=1);

namespace Vehis\MsC\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('msc');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('publisher_name')
                    ->defaultValue('%env(MSC_PUBLISHER_NAME)%')
                    ->info('Service name will be added to each message as it prefix')
                ->end()
                ->arrayNode('queue')
                    ->info('Queue configurations for AMQP transports')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('binding_keys')
                                ->info('Specific binding keys for this queue')
                                ->example(['order.*', 'user.#'])
                                ->prototype('scalar')->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}