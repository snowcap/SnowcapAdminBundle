<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

/**
 * Class HeadingFieldType
 * @package Snowcap\AdminBundle\Datalist\Field\Type
 */
class HeadingFieldType extends TextFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'heading';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'heading';
    }
}