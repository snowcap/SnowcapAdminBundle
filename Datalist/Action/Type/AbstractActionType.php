<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractActionType implements ActionTypeInterface {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }

}