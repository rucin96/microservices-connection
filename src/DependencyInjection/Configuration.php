<?php

declare(strict_types=1);

namespace Vehis\Msc\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('msc');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('publisher_name')->isRequired()->end()
                ->arrayNode('topics')
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
