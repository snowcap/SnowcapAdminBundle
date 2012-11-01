<?php

namespace Snowcap\AdminBundle\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchType extends AbstractType {
    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_content_search';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }
}