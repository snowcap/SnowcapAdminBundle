<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;

class DatalistType implements DatalistTypeInterface {
    /**
     * Build the Datalist view
     *
     * @param \Snowcap\AdminBundle\Datalist\View\DatalistViewInterface $view
     */
    public function buildView(DatalistViewInterface $view)
    {
        // TODO: Implement buildView() method.
    }

    /**
     * Build the Datalist view
     *
     * @param \Snowcap\AdminBundle\Datalist\View\DatalistViewInterface $view
     */
    public function buildDatalist(DatalistBuilder $builder, array $options)
    {
        // TODO: Implement buildDatalist() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }


}