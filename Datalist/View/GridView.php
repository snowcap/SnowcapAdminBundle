<?php

namespace Snowcap\AdminBundle\Datalist\View;

use Snowcap\AdminBundle\Exception;

class GridView implements DatalistViewInterface
{
    protected $columns;

    public function add($path, $type = null, $options = array())
    {
        if ($type === null) {
            $type = 'text';
        }
        if (!isset($options['label'])) {
            $options['label'] = ucfirst($path);
        }
        if ($type === 'text') {
            //nothing special
        }
        elseif ($type === 'label') {
            if(!isset($options['mappings'])) {
                throw new Exception('The "mappings" option is needed for label columns');
            }
        }
        else {
            throw new Exception(sprintf('Unknown type "%s" for datalist %s', $type, get_class($this)));
        }
        $this->columns[] = array(
            'path' => $path,
            'type' => $type,
            'options' => $options
        );
    }

    public function getName()
    {
        return 'grid';
    }

    public function getColumns()
    {
        return $this->columns;
    }

}