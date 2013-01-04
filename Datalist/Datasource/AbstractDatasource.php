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
    protected $limitRange;

    /**
     * @param int $limitPerPage
     * @param int $limitRange
     */
    public function paginate($limitPerPage = 10, $limitRange = 10)
    {
        $this->limitPerPage = $limitPerPage;
        $this->limitRange = $limitRange;

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