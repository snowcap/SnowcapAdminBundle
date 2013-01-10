<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;

class Datalist implements DatalistInterface
{
    /**
     * @var DatalistConfig
     */
    protected $config;

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
     * @var int
     */
    protected $page = 1;

    /**
     * @param string $code
     * @param array $options
     */
    public function __construct(DatalistConfig $config)
    {
        $this->config = $config;
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

        return $this;
    }

    /**
     * @return DatasourceInterface
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        if(!isset($this->datasource)) {
            return new \EmptyIterator();
        }

        $datasource = $this->getDatasource();
        if($this->hasOption('limit_per_page')) {
            $datasource
                ->paginate($this->getOption('limit_per_page'), $this->getOption('range_limit'))
                ->setPage($this->page);
        }

        return $datasource->getIterator();
    }

    /**
     * @return \Snowcap\CoreBundle\Paginator\PaginatorInterface
     */
    public function getPaginator()
    {
        return $this->datasource->getPaginator();
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->config->getOptions();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->config->hasOption($name);
    }

    /**
     * @param string $name
     * @param mixed $default
     */
    public function getOption($name, $default = null)
    {
        return $this->config->getOption($name, $default);
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