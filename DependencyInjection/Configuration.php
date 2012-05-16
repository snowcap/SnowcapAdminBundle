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
                ->scalarNode('bundle')->end()
                ->arrayNode('translation_catalogues')->prototype('scalar')->end()->end()
                ->arrayNode('sections')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
