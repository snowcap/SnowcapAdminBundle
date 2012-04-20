<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class ContentSearchType extends AbstractType {

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'snowcap_admin_content_search';
    }

    /**
     * @param array $options
     * @return string
     */
    public function getParent(array $options)
    {
        return 'form';
    }

    /**
     * @param array $options
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'csrf_protection' => false,
        );
    }


}