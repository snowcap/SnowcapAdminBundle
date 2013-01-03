<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property_path' => null,
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return null;
    }
}