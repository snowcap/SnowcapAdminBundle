<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Doctrine\ORM\QueryBuilder;

use Snowcap\CoreBundle\Paginator\ArrayPaginator;

class ArrayDatasource extends AbstractDatasource
{
    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var array
     */
    private $items = array();

    /**
     * @var \Traversable
     */
    private $iterator;

    /**
     * @var ArrayPaginator
     */
    private $paginator;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Snowcap\CoreBundle\Paginator\ArrayPaginator
     */
    public function getPaginator()
    {
        $this->initialize();

        return $this->paginator;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->initialize();

        return $this->iterator;
    }

    private function initialize() {
        if($this->initialized) {
            return;
        }

        if(isset($this->limitPerPage)) {
            $paginator = new ArrayPaginator($this->items);
            $paginator
                ->setLimitPerPage($this->limitPerPage)
                ->setRangeLimit($this->rangeLimit)
                ->setPage($this->page);

            $this->iterator = $paginator->getIterator();
            $this->paginator = $paginator;
        }
        else {
            $this->iterator = new \ArrayIterator($this->items);
            $this->paginator = null;
        }

        $this->initialized = true;
    }
}