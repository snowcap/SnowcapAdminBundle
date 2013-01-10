<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;

interface DatalistInterface extends \IteratorAggregate
{
    /**
     * @param DatalistFieldInterface $field
     * @return Datalist
     */
    public function addField(DatalistFieldInterface $field);

    /**
     * @param DatasourceInterface $datasource
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
     */
    public function setPage($page);






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