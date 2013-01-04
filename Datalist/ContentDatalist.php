<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\MappingException;

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
     * @var array
     */
    protected $pagination;

    /**
     * @var \Symfony\Component\Form\Form
     */
    protected $searchForm;

    /**
     * @var \Symfony\Component\Form\Form
     */
    protected $filterForm;

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
            if(isset($this->pagination)) { //TODO: find better approach
                $this->paginator = new Paginator($this->queryBuilder->getQuery(), true);
                $this->paginator->setLimitPerPage($this->pagination[0]);
                $this->paginator->setLimitRange($this->pagination[1]);
                $this->paginator->setPage($this->pagination[2]);
            }
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
        $this->pagination = array($limitPerPage, $limitRange, 1);//TODO: find better approach

        return $this;
    }

    /**
     * @param int $page
     */
    public function setPage($page) {
        $this->pagination[2] = $page;
    }

    /**
     * @return \Snowcap\CoreBundle\Manager\PaginatorManager
     */
    public function getPaginator()
    {
        return $this->paginator;
    }


    //TODO: check everything below

    public function setFilterForm(Form $form)
    {
        $this->filterForm = $form;
    }

    public function setSearchForm(Form $form)
    {
        $this->searchForm = $form;
    }

    public function bind(Request $request)
    {
        // Bind search and filter forms
        if(isset($this->searchForm)) {
            $this->searchForm->bind($request);
        }
        if(isset($this->filterForm)) {
            $this->filterForm->bind($request);
        }

        // Set paginator page if GET parameter exists
        if (($page = $request->query->get('page')) !== null) {
            $this->setPage($page);
        }
    }

    public function getContentForm()
    {


        $contentForm = $this->createForm('form', array(), array(
            'virtual' => true,
            'csrf_protection' => false,
        ));
    }
}