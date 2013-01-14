<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

interface DatasourceInterface extends \IteratorAggregate
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
     * @param string $query
     */
    public function setSearchQuery($query);

    /**
     * @return \Snowcap\CoreBundle\Paginator\PaginatorInterface
     */
    public function getPaginator();
}