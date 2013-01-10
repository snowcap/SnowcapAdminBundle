<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;
use Snowcap\AdminBundle\Datalist\TypeInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\DatalistInterface;

interface DatalistTypeInterface extends TypeInterface {
    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     * @return mixed
     */
    public function buildDatalist(DatalistBuilder $builder, array $options);

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistInterface $datalist, array $options);
}