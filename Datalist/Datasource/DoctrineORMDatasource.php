<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Doctrine\ORM\QueryBuilder;

use Snowcap\CoreBundle\Paginator\DoctrineORMPaginator;

class DoctrineORMDatasource extends AbstractDatasource
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var \Traversable
     */
    private $iterator;

    /**
     * @var DoctrineORMPaginator
     */
    private $paginator;

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return \Snowcap\CoreBundle\Paginator\DoctrineORMPaginator
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

    /**
     * Load the collection
     *
     */
    private function initialize()
    {
        if($this->initialized) {
            return;
        }

        if(isset($this->limitPerPage)) {
            $paginator = new DoctrineORMPaginator($this->queryBuilder->getQuery());
            $paginator
                ->setLimitPerPage($this->limitPerPage)
                ->setRangeLimit($this->rangeLimit)
                ->setPage($this->page);
            $this->iterator = $paginator->getIterator();
            $this->paginator = $paginator;
        }
        else {
            $items = $this->queryBuilder->getQuery()->getResult();
            $this->iterator = new \ArrayIterator($items);
            $this->paginator = null;
        }

        $this->initialized = true;
    }
}