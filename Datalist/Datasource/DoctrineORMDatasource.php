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
    public function __construct(QueryBuilder $queryBuilder, array $options = array())
    {
        parent::__construct($options);

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

        // Handle search
        if(isset($this->searchQuery)) {
            if(!isset($this->options['search'])) {
                throw new \Exception('Missing "search" option');
            }

            $search = $this->options['search'];
            if(is_string($search)) {
                $search = array($search);
            }
            if(is_array($search)) {
                foreach($search as $searchField) {
                    $this->queryBuilder->orWhere($this->queryBuilder->expr()->like($searchField, ':query'));
                }
                $this->queryBuilder->setParameter('query', '%'. $this->searchQuery . '%');
            }
            else {
                throw new \InvalidArgumentException(sprintf('Unexpected type "%s" for "search" options', gettype($search)));
            }
        }

        // Handle pagination
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