<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;

class DatalistBuilder {
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Snowcap\AdminBundle\Datalist\View\DatalistViewInterface
     */
    private $view;

    /**
     * @var array
     */
    private $options;

    public function __construct($name, DatalistViewInterface $view, array $options){
        $this->name = $name;
        $this->view = $view;
        $this->options = $options;
    }

    /**
     * @param $field
     * @param string $type
     * @param array $options
     */
    public function addField($field, $type, array $options = array())
    {
        $this->fields[$field] = array(
            'type' => $type,
            'options' => $options
        );
    }

    public function getDatalist()
    {
        $datalist = new Datalist($this->name, $options);
        foreach($this->fields as $field => $fieldConfig){
            $datalist->add($field, $fieldConfig['type'], $fieldConfig['options']);
        }
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}