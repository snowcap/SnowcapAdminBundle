<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

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
     * @param int $limitPerPage
     * @param int $limitRange
     *
     * @return AbstractDatasource
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
}