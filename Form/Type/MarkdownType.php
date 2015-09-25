<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class MarkdownType
 * @package Snowcap\AdminBundle\Form\Type
 */
class MarkdownType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_markdown';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }
}