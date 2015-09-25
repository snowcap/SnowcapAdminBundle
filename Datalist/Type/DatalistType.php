<?php

namespace Snowcap\AdminBundle\Datalist\Type;

/**
 * Class DatalistType
 * @package Snowcap\AdminBundle\Datalist\Type
 */
class DatalistType extends AbstractDatalistType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'datalist';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'datalist';
    }
}