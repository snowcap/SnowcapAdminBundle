<?php

namespace Snowcap\AdminBundle\Datalist\View;

interface DatalistViewInterface {
    public function add($path, $type = null, $options = array());
    public function getName();
}