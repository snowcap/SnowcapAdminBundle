<?php
namespace Snowcap\AdminBundle\Admin;

use Symfony\Bundle\DoctrineBundle\Registry,
    Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Environment,
    Snowcap\AdminBundle\Grid\Factory as GridFactory;

abstract class Base {
    /**
     * @var \Snowcap\AdminBundle\Environment
     */
    protected $environment;
    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $formFactory;
    /**
     * @var \Snowcap\AdminBundle\Grid\Factory
     */
    protected $gridFactory;
    /**
     * @var array
     */
    protected $params;

    protected $doctrine;
    /**
     * @param \Snowcap\AdminBundle\Environment $environment
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Snowcap\AdminBundle\Grid\Factory $gridFactory
     * @param array $params
     */
    public function __construct(array $params, FormFactory $formFactory, GridFactory $gridFactory, Registry $doctrine, $environment)
    {
        $this->formFactory = $formFactory;
        $this->gridFactory = $gridFactory;
        $this->params = $params;
        $this->doctrine = $doctrine;
        $this->environment = $environment;
    }

    public function getParam($paramName)
    {
        return $this->params[$paramName];
    }
    /**
     * @param grid $type
     * @return \Snowcap\AdminBundle\Grid\Base
     */
    public function createGrid($type)
    {
        return $this->gridFactory->create($type);
    }

    public function createForm($type, $data = null, $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }
}