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
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            $label = isset($tag[0]['label'])
                ? $tag[0]['label']
                : $serviceId;
            $definition->addMethodCall('registerAdmin', array($alias, new Reference($serviceId), array(
                'label' => $label,
            )));
        }
    }
}