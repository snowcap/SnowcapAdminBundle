<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Represent an Url field type for the multi upload form type
 *
 * Class MultiUploadUrlType
 * @package Snowcap\AdminBundle\Form\Type
 */
class MultiUploadUrlType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'snowcap_admin_multiupload_url';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }
}
