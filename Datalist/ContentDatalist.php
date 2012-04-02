<?php

namespace Snowcap\AdminBundle\Datalist;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use Snowcap\AdminBundle\Exception;
use Snowcap\CoreBundle\Manager\PaginatorManager;

class ContentDatalist extends AbstractDatalist
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var PaginatorManager
     */
    protected $paginator;


    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getData()
    {
        if (!isset($this->queryBuilder) || !$this->queryBuilder instanceof QueryBuilder) {
            throw new Exception(sprintf('The "%s" list must have a valid queryBuilder property (see Snowcap\AdminBundle\Grid\Content::setQueryBuilder)', $this->name));
        }
        if (!isset($this->data)) {
            try {
                if ($this->paginator !== null) {
                    $this->paginator->setQuery($this->queryBuilder->getQuery());
                    $this->data = $this->paginator->getResult();
                } else {
                    $this->data = $this->queryBuilder->getQuery()->getResult();
                }
            }
            catch (\Doctrine\ORM\Query\QueryException $e) {
                throw new Exception(sprintf('The "%s" list queryBuilder leads to an invalid query (probably due to lacking select or from clauses). The returned error was: %s', $this->name, $e->getMessage()));
            }
        }
        return $this->data;
    }

    public function filterData(array $data, $glue = 'AND')
    {
        if(isset($this->data)){
            throw new Exception(sprintf('A content datalist cannot be filtered if its data has already been initialized'));
        }
        foreach($data as $field => $value){
            switch ($glue) {
                case 'AND':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($field, $this->queryBuilder->expr()->literal('%' . $value . '%')));
                    break;
                case 'OR':
                    $this->queryBuilder->orWhere($this->queryBuilder->expr()->like($field, $this->queryBuilder->expr()->literal('%' . $value . '%')));
                    break;
                default:
                    throw new InvalidArgumentException('Filter data only accept AND/OR glue');
                    break;
            }
        }
    }

    public function paginate($limitPerPage = 10)
    {
        $this->paginator = new PaginatorManager();
        $this->paginator->setLimitPerPage($limitPerPage);
    }

    public function setPage($page) {
        $this->paginator->setPage($page);
    }

    /**
     * @return \Snowcap\CoreBundle\Manager\PaginatorManager
     */
    public function getPaginator()
    {
        return $this->paginator;
    }


}