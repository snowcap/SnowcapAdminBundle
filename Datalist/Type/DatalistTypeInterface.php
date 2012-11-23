<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;

interface DatalistTypeInterface {
    /**
     * Build the Datalist view
     *
     * @param \Snowcap\AdminBundle\Datalist\View\DatalistViewInterface $view
     */
    public function buildDatalist(DatalistBuilder $builder, array $options);

    /**
     * @return string
     */
    public function getName();
}