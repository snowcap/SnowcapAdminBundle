<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;

class Datalist implements DatalistInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var DatasourceInterface
     */
    protected $datasource;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $actions = array();

    /**
     * @param string $code
     * @param array $options
     */
    public function __construct($name, DatalistTypeInterface $type, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @param DatalistFieldInterface $field
     * @return Datalist
     */
    public function addField(DatalistFieldInterface $field)
    {
        $this->fields[]= $field;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param DatasourceInterface $datasource
     */
    public function setDatasource($datasource)
    {
        $this->datasource = $datasource;
    }

    /**
     * @return DatasourceInterface
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    public function getIterator()
    {
        return $this->getDatasource()->getIterator();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist', $name));
        }

        return $this->options[$name];
    }

    public function addAction($routeName, array $parameters = array(), array $options = array())
    {
        $options = array_merge(array(
            'confirm' => false,
            'confirm_title' => 'content.actions.confirm.title',
            'confirm_body' => 'content.actions.confirm.body',
            'confirm_confirm' => 'content.actions.confirm.confirm',
            'confirm_cancel' => 'content.actions.confirm.cancel',
        ), $options);
        if (!array_key_exists('label', $options)) {
            $options['label'] = ucfirst($routeName);
        }
        $this->actions[$routeName] = array('parameters' => $parameters, 'options' => $options);

        return $this;
    }

    public function removeAction($routeName)
    {
        if (array_key_exists($routeName, $this->actions)) {
            unset($this->actions[$routeName]);
        }
    }

    public function getActions()
    {
        return $this->actions;
    }

}