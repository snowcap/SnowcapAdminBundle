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