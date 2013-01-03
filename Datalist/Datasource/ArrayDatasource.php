<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Doctrine\ORM\QueryBuilder;

class ArrayDatasource implements DatasourceInterface
{
    /**
     * @var array
     */
    private $items = array();

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}