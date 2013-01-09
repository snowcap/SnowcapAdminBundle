<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimeFieldType extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'datetime';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'format' => 'd/m/Y'
        ));
    }
}