<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

class HeadingFieldType extends AbstractFieldType
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