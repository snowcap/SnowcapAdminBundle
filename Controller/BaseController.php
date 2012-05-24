<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

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
     */
    public function setFlash($name, $value, $parameters = array())
    {
        return $this->getRequest()->getSession()->setFlash($name, $this->get('translator')->trans($value, $parameters, 'SnowcapAdminBundle'));
    }

    /**
     * Find a content-specific template name in the child bundle
     *
     * @param string $templateName the original template name as used in SnowcapAdminBundle
     * @param string $code the specific content admin code
     * @return string
     */
    protected function getTemplate($templateName, $code = null)
    {
        $templateNameParts = explode(':', $templateName);
        $templateNameParts[0] = $this->get('snowcap_admin')->getBundle();
        if(null !== $code) {
            $templateNameParts[1] .= '/' . $code;
        }
        $candidate = implode(':', $templateNameParts);
        if($this->get('templating')->exists($candidate)) {
            return $candidate;
        }
        return $templateName;
    }

    public function renderError($type, $code)
    {
        $translatedTitle = $this->get('translator')->trans($type . '.title', array(), 'SnowcapAdminBundle');
        $translatedMessages = $this->get('translator')->trans($type . '.message', array(), 'SnowcapAdminBundle');
        return new Response($this->renderView('SnowcapAdminBundle:Error:' . $code . '.html.twig', array('title' => $translatedTitle, 'message' => $translatedMessages)), $code);
    }
}