<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface;

abstract class AbstractDatasource implements DatasourceInterface
{
    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $limitPerPage;

    /**
     * @var int
     */
    protected $rangeLimit;

    /**
     * @var string
     */
    protected $searchQuery;

    /**
     * @var ExpressionInterface
     */
    protected $filterExpression;

    /**
     * @var ExpressionInterface
     */
    protected $searchExpression;

    /**
     * @var \Traversable
     */
    protected $iterator;

    /**
     * @var \Snowcap\CoreBundle\Paginator\PaginatorInterface
     */
    protected $paginator;

    /**
     * @param int $limitPerPage
     * @param int $rangeLimit
     *
     * @return DatasourceInterface
     */
    public function paginate($limitPerPage, $rangeLimit)
    {
        $this->limitPerPage = $limitPerPage;
        $this->rangeLimit = $rangeLimit;

        return $this;
    }

    /**
     * @param int $page
     *
     * @return DatasourceInterface
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     */
    public function setSearchExpression(ExpressionInterface $expression)
    {
        $this->searchExpression = $expression;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     */
    public function setFilterExpression(ExpressionInterface $expression)
    {
        $this->filterExpression = $expression;
    }

    /**
     * This method should populated the iterator and paginator member variables
     */
    abstract protected function initialize();

    /**
     * @return int
     */
    public function count()
    {
        $this->initialize();

        return count($this->iterator);
    }
}