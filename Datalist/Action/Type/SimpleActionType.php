<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleActionType extends AbstractActionType {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('route'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simple';
    }
}