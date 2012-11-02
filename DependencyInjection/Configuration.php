<?php
namespace Snowcap\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration handling class for the admin config as defined in the application main config file
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('snowcap_admin');

        $rootNode
            ->children()
                ->arrayNode('content')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route_prefix')->defaultValue('/admin')->end()
                        ->scalarNode('route_name_prefix')->defaultValue('snowcap_admin')->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
