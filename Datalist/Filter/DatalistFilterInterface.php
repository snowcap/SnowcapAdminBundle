<?php

namespace Snowcap\AdminBundle\Datalist\Filter;

interface DatalistFilterInterface
{
    /**
     * @return \Snowcap\AdminBundle\Datalist\Filter\Type\FilterTypeInterface
     */
    public function getType();

    /**
     * @return \Snowcap\AdminBundle\Datalist\DatalistInterface
     */
    public function getDatalist();

    /**
     * @return string
     */
    public function getName();
}