<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatalistType extends AbstractDatalistType {
    /**
     * @return string
     */
    public function getName()
    {
        return 'datalist';
    }
}