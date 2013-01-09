<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\TypeInterface;

interface FieldTypeInterface extends TypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getParent();
}