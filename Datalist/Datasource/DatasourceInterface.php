<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

interface DatasourceInterface extends \IteratorAggregate
{
    /**
     * @param int $limitPerPage
     * @param int $limitRange
     */
    public function paginate($limitPerPage = 10, $limitRange = 10);

    /**
     * @param int $page
     */
    public function setPage($page);
}