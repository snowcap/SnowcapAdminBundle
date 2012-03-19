<?php
namespace Snowcap\AdminBundle\Admin;

use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Environment;
use Snowcap\AdminBundle\Exception;

abstract class AbstractAdmin
{

    /**
     * @var string
     */
    protected $code;

    /**
     * @var \Snowcap\AdminBundle\Environment
     */
    protected $environment;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param string $code
     * @param array $params
     * @param \Snowcap\AdminBundle\Environment $environment
     */
    public function __construct($code, array $params, Environment $environment)
    {
        $this->code = $code;
        $this->validateParams($params); //TODO: check if necessary
        $this->params = $params;
        $this->environment = $environment;
    }

    /**
     * Return the admin code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get a simple param for this admin instance
     *
     * @param $paramName
     * @return mixed
     * @throws \Snowcap\AdminBundle\Exception
     */
    public function getParam($paramName)
    {
        if (!array_key_exists($paramName, $this->params)) {
            throw new Exception(sprintf('The admin section %s must have a %s parameter', $this->getCode(), $paramName), Exception::SECTION_INVALID);
        }
        return $this->params[$paramName];
    }

    /**
     * Validate the admin params - to be overriden in child classes
     *
     * @param array $params
     * @throws \Snowcap\AdminBundle\Exception
     */
    protected function validateParams(array $params)
    {
        if (!array_key_exists('label', $params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "label" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
    }

    /**
     * Creates a form based on the provided parameters
     *
     * @param string|\Symfony\Component\Form\FormTypeInterface $type
     * @param $data
     * @param array $options
     * @return \Symfony\Component\Form\Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        $form = $this->environment->get('form.factory')->create($type, $data, $options);
        return $form;
    }

    /**
     * Create a datalist with the provided view mode and name
     *
     * @param string $view a registered grid view mode
     * @param string $name
     * @return \Snowcap\AdminBundle\Datalist\AbstractDatalist
     */
    protected function createDatalist($view, $name)
    {
        $datalist = $this->environment->get('snowcap_admin.datalist_factory')->create($view, $name);
        $datalist->setQueryBuilder($this->getQueryBuilder());
        $datalist->addAction(
            'snowcap_admin_content_update',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.edit', 'icon' => 'icon-edit')
        );
        $datalist->addAction(
            'snowcap_admin_content_delete',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.delete', 'icon' => 'icon-remove')
        );

        return $datalist;
    }

    /**
     * Get the default route, to be used in menus
     *
     * @return string
     */
    abstract public function getDefaultRoute();

}