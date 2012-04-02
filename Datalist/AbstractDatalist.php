<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Exception;
use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;

class AbstractDatalist
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var \Snowcap\AdminBundle\Datalist\View\DatalistViewInterface
     */
    protected $view;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @param string $code
     * @param string $view
     */
    public function __construct($name, DatalistViewInterface $view)
    {
        $this->name = $name;
        $this->view = $view;
    }

    /**
     * @param string $name
     * @param string $type
     * @return AbstractDatalist
     */
    public function add($path, $type, array $options = array())
    {
        $this->view->add($path, $type, $options);
        return $this;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Snowcap\AdminBundle\Datalist\View\DatalistViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    public function addAction($routeName, array $parameters = array(), array $options = array())
    {
        if (!array_key_exists('label', $options)) {
            $options['label'] = ucfirst($routeName);
        }
        $this->actions[$routeName] = array('parameters' => $parameters, 'options' => $options);
    }

    public function getActions()
    {
        return $this->actions;
    }

}