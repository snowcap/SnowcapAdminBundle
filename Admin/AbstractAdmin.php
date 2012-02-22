<?php
namespace Snowcap\AdminBundle\Admin;

use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Environment;
use Snowcap\AdminBundle\Grid\Factory as GridFactory;
use Snowcap\AdminBundle\Exception;

abstract class AbstractAdmin
{
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

    protected $code;

    /**
     * @param \Snowcap\AdminBundle\Environment $environment
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Snowcap\AdminBundle\Grid\Factory $gridFactory
     * @param array $params
     */
    public function __construct($code, array $params, Environment $environment)
    {
        $this->code = $code;
        $this->validateParams($params); //TODO: check if necessary
        $this->params = array_merge($this->getDefaultParams(), $params);
        $this->environment = $environment;
    }

    protected function getDefaultParams()
    {
        return array();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getParam($paramName)
    {
        if (!array_key_exists($paramName, $this->params)) {
            throw new Exception(sprintf('The admin section %s must have a %s parameter', $this->getCode(), $paramName), Exception::SECTION_INVALID);
        }
        return $this->params[$paramName];
    }

    /**
     * @param grid $type
     * @return \Snowcap\AdminBundle\Grid\Base
     */
    public function createGrid($type, $code)
    {
        return $this->environment->createGrid($type, $code);
    }

    /**
     * @param $type
     * @param null $data
     * @param array $options
     * @return \Symfony\Component\Form\Form
     */
    public function createForm($type, $data = null, $options = array())
    {
        return $this->environment->createForm($type, $data, $options);
    }

    abstract public function getDefaultPath();

    protected function validateParams(array $params)
    {
        if (!array_key_exists('label', $params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "label" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
    }
}