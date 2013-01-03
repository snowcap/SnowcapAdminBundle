<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

class DateTimeFieldType extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'datetime';
    }
}