<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;

interface DatalistTypeInterface {

    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     * @return mixed
     */
    public function buildDatalist(DatalistBuilder $builder, array $options);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getParent();
}