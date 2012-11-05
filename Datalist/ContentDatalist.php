<?php

namespace Snowcap\AdminBundle\Datalist;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\MappingException;

use Snowcap\CoreBundle\Paginator\Paginator;

class ContentDatalist extends AbstractDatalist
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var Paginator
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
            throw new \Exception(sprintf('The "%s" list must have a valid queryBuilder property (see Snowcap\AdminBundle\Grid\Content::setQueryBuilder)', $this->name));
        }
        if (!isset($this->data)) {
            try {
                if ($this->paginator !== null) {
                    $this->data = $this->paginator;
                } else {
                    $this->data = $this->queryBuilder->getQuery()->getResult();
                }
            }
            catch (\Doctrine\ORM\Query\QueryException $e) {
                throw new \Exception(sprintf('The "%s" list queryBuilder leads to an invalid query. The returned error was: %s', $this->name, $e->getMessage()));
            }
        }
        return $this->data;
    }

    /**
     * Filter the datalist data
     *
     * @param array $filters
     * @param string $glue
     * @throws \Snowcap\AdminBundle\Exception|\Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function filterData(array $filters, $glue = 'AND')
    {
        $queryBuilder = $this->queryBuilder;
        $filters = array_filter($filters, function($filter){
            return !empty($filter['value']);
        });
        array_walk($filters, function(&$filter) use($queryBuilder) {
            $value = $filter['value'];
            if(is_object($value)) {
                try {
                    $metaData = $queryBuilder->getEntityManager()->getClassMetaData(get_class($value));
                    $filter['value'] = $value->getId();
                    return $filter;
                }
                catch(MappingException $e){} // do nothing
            }

        });

        if(isset($this->data)){
            throw new \Exception(sprintf('A content datalist cannot be filtered if its data has already been initialized'));
        }
        foreach($filters as $filter){
            $placeholder = str_replace('.','_',$filter['field']);
            switch($filter['operator']) {
                case '=':
                    $expression = $this->queryBuilder->expr()->eq($filter['field'], ':' . $placeholder);
                    $this->queryBuilder->setParameter($placeholder, $filter['value']);
                    break;
                case 'LIKE':
                    $expression = $this->queryBuilder->expr()->like($filter['field'], ':' . $placeholder);
                    $this->queryBuilder->setParameter($placeholder, '%' . $filter['value'] . '%');
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Filter operator "%s" not recognized', $filter['operator']));
                    break;
            }

            switch ($glue) {
                case 'AND':
                    $this->queryBuilder->andWhere($expression);
                    break;
                case 'OR':
                    $this->queryBuilder->orWhere($expression);
                    break;
                default:
                    throw new \InvalidArgumentException('Filter data only accept AND/OR glue');
                    break;
            }
        }
    }

    /**
     * @param int $limitPerPage
     */
    public function paginate($limitPerPage = 10, $limitRange = 10)
    {
        $this->paginator = new Paginator($this->queryBuilder->getQuery(), true);
        $this->paginator->setLimitPerPage($limitPerPage);
        $this->paginator->setLimitRange($limitRange);
        return $this;
    }

    /**
     * @param int $page
     */
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