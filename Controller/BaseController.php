<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class BaseController extends Controller
{

    /**
     * Sets a translateed flash message.
     *
     * @param string $name
     * @param string $value
     * @param array $params
     */
    public function setFlash($name, $value, $parameters = array())
    {
        return $this->getRequest()->getSession()->setFlash($name, $this->get('translator')->trans($value, $parameters, 'SnowcapAdminBundle'));
    }
}