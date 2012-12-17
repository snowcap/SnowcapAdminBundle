<?php

namespace Snowcap\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Snowcap\AdminBundle\DependencyInjection\Compiler\AdminCompilerPass;
use Snowcap\AdminBundle\DependencyInjection\Compiler\DatalistCompilerPass;
use Snowcap\AdminBundle\DependencyInjection\Compiler\ImCompilerPass;

class SnowcapAdminBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AdminCompilerPass());
        $container->addCompilerPass(new DatalistCompilerPass());
        $container->addCompilerPass(new ImCompilerPass());
    }
}
