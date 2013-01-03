<?php

namespace Snowcap\AdminBundle\Datalist\Field;

use Snowcap\AdminBundle\Datalist\DatalistInterface;

interface DatalistFieldInterface
{
    /**
     * @return \Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface
     */
    public function getType();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * @return string
     */
    public function getPropertyPath();

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     */
    public function setDatalist(DatalistInterface $datalist);

    /**
     * @return DatalistInterface
     */
    public function getDatalist();
}