<?php
namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * The default admin controller is used as a dashboard for
 * admin users, and provides a few utilities methods for interface purposes
 * 
 */
class DefaultController extends BaseController
{
    /**
     * Admin default action
     *
     * @Template()
     *
     * @return mixed
     */
    public function indexAction()
    {
        return array();
    }

    public function markdownAction() {
		$content = $this->getRequest()->request->get("content");
		$result = $this->container->get('markdown.parser')->transform($content);
		return new Response($result);
    }

    public function switchLocaleAction($locale) {
        $this->getRequest()->setLocale($locale);
        $referer = $this->getRequest()->headers->get('referer');
        return $this->redirect($referer);
    }

}
