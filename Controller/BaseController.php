<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Snowcap\CoreBundle\Util\String;

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
     *
     * //TODO: replace by getSession() usage
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
     *
     * @return string
     */
    protected function getTemplate($templateName, $code = null)
    {
        $kernel = $this->get('kernel');
        foreach($kernel->getBundles() as $bundle) {
            if('SnowcapAdminBundle' === $bundle->getParent()) {
                $adminBundleName = $bundle->getName();
                $templateNameParts = explode(':', $templateName);
                $templateNameParts[0] = $adminBundleName;
                if(null !== $code) {
                    $templateNameParts[1] .= '/' . String::camelize($code);
                }
                $candidate = implode(':', $templateNameParts);
                if($this->get('templating')->exists($candidate)) {
                    return $candidate;
                }
            }
        }

        return $templateName;
    }

    /**
     * @param $type
     * @param $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @TODO: check if still relevant
     */
    public function renderError($type, $code)
    {
        $translatedTitle = $this->get('translator')->trans($type . '.title', array(), 'SnowcapAdminBundle');
        $translatedMessages = $this->get('translator')->trans($type . '.message', array(), 'SnowcapAdminBundle');

        return new Response($this->renderView('SnowcapAdminBundle:Error:' . $code . '.html.twig', array('title' => $translatedTitle, 'message' => $translatedMessages)), $code);
    }
}