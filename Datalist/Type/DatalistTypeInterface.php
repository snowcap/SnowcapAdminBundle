<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;
use Snowcap\AdminBundle\Datalist\TypeInterface;

interface DatalistTypeInterface extends TypeInterface {

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     * @return mixed
     */
    public function buildDatalist(DatalistBuilder $builder, array $options);

    /**
     * @return string
     */
    public function getParent();
}