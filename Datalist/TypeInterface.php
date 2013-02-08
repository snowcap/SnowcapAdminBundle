<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

interface TypeInterface {
    /**
     * @return string
     */
    public function getName();

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @return string
     */
    public function getBlockName();
}