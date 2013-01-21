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
     * @var array
     */
    protected $options;

    /**
     * @var \Traversable
     */
    protected $iterator;

    /**
     * @var \Snowcap\CoreBundle\Paginator\PaginatorInterface
     */
    protected $paginator;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->processOptions($options);
    }

    /**
     * @param int $limitPerPage
     * @param int $limitRange
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
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param string $query
     */
    public function setSearchQuery($query)
    {
        $this->searchQuery = $query;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return mixed
     */
    public function setFilterExpression(ExpressionInterface $expression)
    {
        $this->filterExpression = $expression;
    }

    /**
     * @param array $options
     */
    protected function processOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setOptional(array('search', 'search_mode'))
            ->setAllowedTypes(array(
                'search' => array('string', 'array')
            ));

        $this->options = $resolver->resolve($options);
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