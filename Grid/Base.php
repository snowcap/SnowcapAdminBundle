<?php
namespace Snowcap\AdminBundle\Grid;

use Symfony\Component\Form\FormFactory;
use \Doctrine\ORM\QueryBuilder;

abstract class Base {
    /**
     * @var array
     */
    protected $columns;

    protected $options;

    protected $data;

    protected $actions;
    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $formFactory;

    public function addColumn($columnName, $columnParams = array())
    {
        $this->columns[$columnName] = $columnParams;
        return $this;
    }

    public function addAction($routeName, $routeParameterss, $routeLabel, $icon) {
        $this->actions[$routeName] = array('parameters' => $routeParameterss, 'label' => $routeLabel, 'icon' => $icon);
    }

    public function getActions()
    {
        return $this->actions;
    }
    

    public function setOption($optionName, $optionParams)
    {
        $this->options[$optionName] = $optionParams;
        return $this;
    }

    public function getOption($optionName)
    {
        return $this->options[$optionName];
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}