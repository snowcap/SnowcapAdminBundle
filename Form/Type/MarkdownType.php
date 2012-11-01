<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

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