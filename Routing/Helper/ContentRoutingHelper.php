<?php

namespace Snowcap\AdminBundle\Routing\Helper;

use Symfony\Component\Routing\Route;

use Snowcap\AdminBundle\Admin\ContentAdmin;

class ContentRoutingHelper {
    /**
     * @var string
     */
    private $routeNamePrefix;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @param string $routeNamePrefix
     * @param string $routePrefix
     */
    public function __construct($routePrefix, $routeNamePrefix) {
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = $routeNamePrefix;
    }

    public function getRouteName(ContentAdmin $admin, $action)
    {
        return $this->routeNamePrefix . '_' . $admin->getAlias() . '_' . $action;
    }

    public function getRoute(ContentAdmin $admin, $action)
    {
        return new Route('/' . $admin->getAlias() . '/' . $action, array('_controller' => 'SnowcapAdminBundle:Content:' . $action, 'alias' => $admin->getAlias()));
    }

}