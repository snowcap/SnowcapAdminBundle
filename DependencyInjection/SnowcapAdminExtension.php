<?php

namespace Snowcap\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;

/**
 * Extension class for the admin configuration
 * 
 */
class SnowcapAdminExtension extends Extension
{
    /**
     * Load the config data for the admin bundle
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /*foreach (array('sections','bundle') as $attribute) {
            $container->setParameter($attribute , $config[$attribute]);
        }

        if(array_key_exists('translation_catalogues', $config)) {
            $container->setParameter('translation_catalogues', $config['translation_catalogues']);
        }*/

    }
}
