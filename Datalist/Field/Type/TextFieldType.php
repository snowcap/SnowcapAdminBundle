<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

class TextFieldType extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'text';
    }
}