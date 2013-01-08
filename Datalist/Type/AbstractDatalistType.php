<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;

abstract class AbstractDatalistType implements DatalistTypeInterface {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     */
    public function buildDatalist(DatalistBuilder $builder, array $options)
    {

    }

    /**
     * @return string
     */
    public function getParent()
    {
        return null;
    }
}