<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\Form\Form;

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
     * @return array
     */
    public function getFields();

    /**
     * @param Filter\DatalistFilterInterface $filter
     * @return DatalistInterface
     */
    public function addFilter(DatalistFilterInterface $filter);

    /**
     * @return array
     */
    public function getFilters();

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
     * @return bool
     */
    public function isSearchable();

    /**
     * @return bool
     */
    public function isFilterable();



    public function setSearchForm(Form $form);

    public function setFilterForm(Form $form);

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getSearchForm();

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getFilterForm();

    /**
     * @param mixed $data
     * @return DatalistInterface
     */
    public function bind($data);



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