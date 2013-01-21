<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface;

interface DatasourceInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param int $limitPerPage
     * @param int $limitRange
     *
     * @return DatasourceInterface
     */
    public function paginate($limitPerPage, $rangeLimit);

    /**
     * @param int $page
     */
    public function setPage($page);

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return mixed
     */
    public function setSearchExpression(ExpressionInterface $expression);

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return mixed
     */
    public function setFilterExpression(ExpressionInterface $expression);

    /**
     * @return \Snowcap\CoreBundle\Paginator\PaginatorInterface
     */
    public function getPaginator();
}