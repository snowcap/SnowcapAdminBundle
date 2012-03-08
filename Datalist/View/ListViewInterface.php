<?php

namespace Snowcap\AdminBundle\Datalist\View;

interface ListViewInterface {
    public function add($path, $type = null, $options = array());
    public function getName();
}