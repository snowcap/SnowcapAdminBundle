<?php

namespace Snowcap\AdminBundle\Routing\Helper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;

use Snowcap\AdminBundle\Admin\ContentAdmin;

class ContentRoutingHelper {
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

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
    public function __construct(RouterInterface $router, $routePrefix, $routeNamePrefix) {
        $this->router = $router;
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = $routeNamePrefix;
    }

    public function getRouteName(ContentAdmin $admin, $action)
    {
        return $this->routeNamePrefix . '_' . $admin->getAlias() . '_' . $action;
    }

    public function getRoute(ContentAdmin $admin, $action, $params = array(), $defaultRoute = false)
    {
        $pattern = '/' . $admin->getAlias();
        if(!$defaultRoute) {
            $pattern .= '/' . $action;
        }
        foreach($params as $param) {
            $pattern .= '/{' . $param . '}';
        }

        return new Route($pattern, array('_controller' => 'SnowcapAdminBundle:Content:' . $action, 'alias' => $admin->getAlias()));
    }

    /**
     * @param \Snowcap\AdminBundle\Admin\ContentAdmin $admin
     * @param string $action
     * @return string
     */
    public function generateUrl(ContentAdmin $admin, $action, $params = array())
    {
        $routeName = $this->getRouteName($admin, $action);

        return $this->router->generate($routeName, $params);
    }
}