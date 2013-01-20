<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;
use Snowcap\AdminBundle\Datalist\TypeInterface;

interface ActionTypeInterface extends TypeInterface {
    /**
     * @param \Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param $item
     * @param array $options
     * @return string
     */
    public function getUrl(DatalistActionInterface $action, $item, array $options = array());
}