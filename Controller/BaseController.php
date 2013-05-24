<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller provides generic capabilities for admin controllers
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
     * @param string $domain
     */
    public function setFlash($name, $value, $parameters = array(), $domain = 'SnowcapAdminBundle') //TODO: replace by getSession() usage
    {
        return $this->getRequest()->getSession()->setFlash($name, $this->get('translator')->trans($value, $parameters, $domain));
    }

    /**
     * @param $type
     * @param $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderError($type, $code) //TODO: check if still relevant
    {
        $translatedTitle = $this->get('translator')->trans($type . '.title', array(), 'SnowcapAdminBundle');
        $translatedMessages = $this->get('translator')->trans($type . '.message', array(), 'SnowcapAdminBundle');

        return new Response($this->renderView('SnowcapAdminBundle:Error:' . $code . '.html.twig', array('title' => $translatedTitle, 'message' => $translatedMessages)), $code);
    }
}
