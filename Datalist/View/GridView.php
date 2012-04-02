<?php

namespace Snowcap\AdminBundle\Datalist\View;

class GridView implements DatalistViewInterface {
    protected $columns;
    public function add($path, $type = null, $options = array())
    {
        if($type === null) {
            $type = 'text';
        }
        if(!isset($options['label'])) {
            $options['label'] = ucfirst($path);
        }
        $this->columns[]= array(
            'path' => $path,
            'type' => $type,
            'options' => $options
        );
    }

    public function getName()
    {
        return 'grid';
    }

    public function getColumns(){
        return $this->columns;
    }

}