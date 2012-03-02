<?php

namespace Snowcap\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilder;


class CollectionTypeExtension extends AbstractTypeExtension {
    public function getExtendedType(){
        return 'collection';
    }

    public function getDefaultOptions(array $options){
        return array('initial_data' => null);
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        if (isset($options['initial_data'])) {
            $prototype = $builder->getAttribute('prototype');
            $prototype->setData($options['initial_data']);
            $builder->setAttribute('prototype', $prototype);
        }
    }


}