<?php
namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Snowcap\CoreBundle\Paginator\Paginator;

/**
 * The default admin controller is used as a dashboard for
 * admin users, and provides a few utilities methods for interface purposes
 * 
 */
class NavigationController extends BaseController
{
    /**
     * Get the navigation for content management
     *
     * @Template()
     *
     * @return mixed
     */
    public function mainAction() {
        return array(
            'admins' => $this->get('snowcap_admin')->getAdmins(),
        );
    }
}
