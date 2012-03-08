<?php

namespace Snowcap\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DatalistViewCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     *
     * @api
     */
    function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('snowcap_admin.datalist_factory')) {
            return;
        }
        $definition = $container->getDefinition('snowcap_admin.datalist_factory');
        $views = array();
        foreach ($container->findTaggedServiceIds('snowcap_admin.datalist_view') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            $views[$alias] = new Reference($serviceId);
        }
        $definition->replaceArgument(0, $views);
    }

}