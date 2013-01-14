<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;
use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;

interface DatalistInterface extends \IteratorAggregate
{
    /**
     * @return DatalistTypeInterface
     */
    public function getType();

    /**
     * @param DatalistFieldInterface $field
     * @return DatalistInterface
     */
    public function addField(DatalistFieldInterface $field);

    /**
     * @param Filter\DatalistFilterInterface $filter
     * @return DatalistInterface
     */
    public function addFilter(DatalistFilterInterface $filter);

    /**
     * @param DatasourceInterface $datasource
     *
     * @return DatalistInterface
     */
    public function setDatasource($datasource);

    /**
     * @return DatasourceInterface
     */
    public function getDatasource();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name);

    /**
     * @param string $name
     * @param mixed $default
     */
    public function getOption($name, $default = null);

    /**
     * @param int $page
     *
     * @return DatalistInterface
     */
    public function setPage($page);

    /**
     * @param string $query
     *
     * @return DatalistInterface
     */
    public function setSearchQuery($query);




    public function addAction($routeName, array $parameters = array(), array $options = array());

    /**
     * @param string $routeName
     * @return mixed
     */
    public function removeAction($routeName);

    /**
     * @return array
     */
    public function getActions();
}