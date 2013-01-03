<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Doctrine\ORM\QueryBuilder;

class DoctrineORMDatasource implements DatasourceInterface
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
     * @var array
     */
    private $items = array();

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->initialize();

        return new \ArrayIterator($this->items);
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

        $this->items = $this->queryBuilder->getQuery()->getResult();
        $this->initialized = true;
    }
}