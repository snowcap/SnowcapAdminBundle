<?php

namespace Snowcap\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('snowcap_admin')) {
            return;
        }

        $definition = $container->getDefinition('snowcap_admin');
        foreach ($container->findTaggedServiceIds('snowcap_admin.admin') as $serviceId => $tag) {
            $adminTag = $tag[0];

            $alias = isset($adminTag['alias'])
                ? $adminTag['alias']
                : $serviceId;

            if(!isset($adminTag['label'])) {
                $adminTag['label'] = $serviceId;
            }

            unset($adminTag['alias']);

            $definition->addMethodCall('registerAdmin', array($alias, new Reference($serviceId), $adminTag));
        }
    }
}