<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatalistType extends AbstractDatalistType {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'display_mode' => 'grid',
            'limit_per_page' => null,
            'range_limit' => 10
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datalist';
    }
}