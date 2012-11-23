<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Exception;
use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;

class Datalist
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options;

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
    public function __construct($name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
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