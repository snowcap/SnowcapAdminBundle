<?php

namespace Snowcap\AdminBundle\Datalist;

use Doctrine\ORM\QueryBuilder;
use Snowcap\AdminBundle\Exception;

class ContentDatalist extends AbstractDatalist
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

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
                $this->data = $this->queryBuilder->getQuery()->getResult();
            }
            catch (\Doctrine\ORM\Query\QueryException $e) {
                throw new Exception(sprintf('The "%s" list queryBuilder leads to an invalid query (probably due to lacking select or from clauses). The returned error was: %s', $this->name, $e->getMessage()));
            }
        }
        return $this->data;
    }

    public function filterData(array $data)
    {
        if(isset($this->data)){
            throw new Exception(sprintf('A content datalist cannot be filtered if its data has already been initialized'));
        }
        foreach($data as $field => $value){
            $this->queryBuilder->add('where', $this->queryBuilder->expr()->like($field, $this->queryBuilder->expr()->literal('%' . $value . '%')));
        }
    }
}