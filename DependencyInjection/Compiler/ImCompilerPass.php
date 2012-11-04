<?php

namespace Snowcap\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ImCompilerPass implements CompilerPassInterface {
    /**
     * Add admin thumb formats to SnowcapImBundle if applicable
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if($container->hasDefinition('snowcap_im.manager')) {
            $imFormats = $container->getParameter('snowcap_admin.im_formats');
            foreach($imFormats as $name => $config) {
                $container->getDefinition('snowcap_im.manager')->addMethodCall('addFormat', array($name, $config));
            }
        }
    }
}