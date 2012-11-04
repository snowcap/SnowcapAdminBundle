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
                ->append($this->addImNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function addImNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('im_formats');

        $defaultAdminThumbConfig = array(
            'thumbnail' => '180x120>',
            'background' => 'transparent',
            'gravity' => 'center',
            'extent' => '180x120'
        );

        $defaultAdminSmallthumbConfig = array(
            'thumbnail' => '50x30>',
            'background' => 'transparent',
            'gravity' => 'center',
            'extent' => '50x30'
        );

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('admin_thumb')
                    ->defaultValue($defaultAdminThumbConfig)
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('admin_smallthumb')
                    ->defaultValue($defaultAdminSmallthumbConfig)
                    ->prototype('variable')->end()
                ->end()
            ->end();

        return $node;
    }
}
