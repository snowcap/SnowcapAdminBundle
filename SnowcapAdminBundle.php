<?php

namespace Snowcap\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Snowcap\AdminBundle\DependencyInjection\Compiler\DatalistViewCompilerPass;

class SnowcapAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DatalistViewCompilerPass());
    }

}
