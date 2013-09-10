<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

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