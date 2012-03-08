<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Exception;

class AbstractDatalist
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var \Snowcap\AdminBundle\Datalist\View\ListViewInterface
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
     * @var array
     */
    protected $availableViews = array(
        'grid' => 'Snowcap\AdminBundle\Datalist\View\GridView',
        'thumbnail' => 'Snowcap\AdminBundle\Datalist\View\ThumbnailView'
    );

    /**
     * @param string $code
     * @param string $view
     */
    public function __construct($name, $view)
    {
        $this->name = $name;
        if (!array_key_exists($view, $this->availableViews)) {
            throw new Exception(sprintf('The datalist view "%s" does not exist', $view));
        }
        $this->view = new $this->availableViews[$view];
    }

    /**
     * @param string $name
     * @param string $type
     * @return AbstractDatalist
     */
    public function add($name, $type)
    {
        $this->view->add($name, $type);
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
     * @return \Snowcap\AdminBundle\Datalist\View\ListViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    public function addAction($routeName, array $parameters = array(), array $options = array())
    {
        if (!array_key_exists('label', $options)) {
            $options['label'] = $routeName;
        }
        $this->actions[$routeName] = array('parameters' => $parameters, 'options' => $options);
    }

    public function getActions()
    {
        return $this->actions;
    }

}