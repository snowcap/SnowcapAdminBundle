<?php
namespace Snowcap\AdminBundle\Grid;

use Symfony\Component\Form\FormFactory;
use \Doctrine\ORM\QueryBuilder;

abstract class AbstractGrid {
    /**
     * @var array
     */
    protected $columns;

    protected $options;

    protected $data;

    protected $actions;

    protected $code;
    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $formFactory;

    public function __construct($code) {
        $this->code = $code;
    }

    public function getCode() {
        return $this->code;
    }

    public function addColumn($path, $options = array())
    {
        $this->columns[$path] = $options;
        return $this;
    }

    public function addAction($routeName, array $parameters = array(), array $options = array()) {
        if(!array_key_exists('label', $options)){
            $options['label'] = $routeName;
        }
        $this->actions[$routeName] = array('parameters' => $parameters, 'options' => $options);
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

    public function hasOption($optionName) {
        return array_key_exists($optionName, $this->options);
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