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
    public function __construct(array $items, array $options = array())
    {
        parent::__construct($options);

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

    private function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $items = $this->items;
        $query = $this->searchQuery;
        // Handle search
        if (isset($this->searchQuery)) {
            if(!isset($this->options['search'])) {
                throw new \Exception('Missing "search" option');
            }

            $search = $this->options['search'];
            if(is_string($search)) {
                $search = array($search);
            }
            if(is_array($search)) {
                $items = array_filter($items, function($item) use($search, $query) {
                    foreach($search as $searchField) {
                        if(isset($item[$searchField]) && $item[$searchField] === $query) {
                            return true;
                        }
                    }

                    return false;
                });
            }
            else {
                throw new \InvalidArgumentException(sprintf('Unexpected type "%s" for "search" options', gettype($search)));
            }
        }

        // Handle pagination
        if (isset($this->limitPerPage)) {
            $paginator = new ArrayPaginator($items);
            $paginator
                ->setLimitPerPage($this->limitPerPage)
                ->setRangeLimit($this->rangeLimit)
                ->setPage($this->page);

            $this->iterator = $paginator->getIterator();
            $this->paginator = $paginator;
        } else {
            $this->iterator = new \ArrayIterator($items);
            $this->paginator = null;
        }

        $this->initialized = true;
    }
}