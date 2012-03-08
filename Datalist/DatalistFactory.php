<?php

namespace Snowcap\AdminBundle\Datalist;
use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;
use Snowcap\AdminBundle\Exception;
use Snowcap\AdminBundle\Datalist\ContentDatalist;

class DatalistFactory {
    protected $views;

    public function __construct(array $views = array()){
        $this->views = $views;
    }

    public function createDatalist($name, $view) {
        if(!isset($this->views[$view])){
            throw new Exception(sprintf('The view "%s" has not been registered for the datalist factory service', $view));
        }

        return new ContentDatalist($name, $this->views[$view]);
    }
}