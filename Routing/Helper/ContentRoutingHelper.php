<?php

namespace Snowcap\AdminBundle\Routing\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;

use Snowcap\AdminBundle\Admin\ContentAdmin;

class ContentRoutingHelper {
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser
     */
    private $parser;

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
    public function __construct(RouterInterface $router, ControllerNameParser $parser, $routePrefix, $routeNamePrefix) {
        $this->router = $router;
        $this->parser = $parser;
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = $routeNamePrefix;
    }

    public function getRouteName(ContentAdmin $admin, $action)
    {
        return $this->routeNamePrefix . '_' . $admin->getAlias() . '_' . $action;
    }

    /**
     * Build a route for the given admin and action, with the provided params
     *
     * This method will first attempt to find a custom Route (like "YourCustomAdminBundle:Section:index")
     * and if it does not work
     *
     * @param \Snowcap\AdminBundle\Admin\ContentAdmin $admin
     * @param string $action
     * @param array $params
     * @param bool $defaultRoute
     * @return \Symfony\Component\Routing\Route
     */
    public function getRoute(ContentAdmin $admin, $action, $params = array(), $defaultRoute = false)
    {
        $pattern = '/' . $admin->getAlias();
        if(!$defaultRoute) {
            $pattern .= '/' . $action;
        }
        foreach($params as $param) {
            $pattern .= '/{' . $param . '}';
        }

        preg_match('/(?:[A-Z](?:[A-Za-z0-9])+\\\)*(?:)[A-Z](?:[A-Za-z0-9])+Bundle/', get_class($admin), $matches);
        $bundle = implode('', explode('\\', $matches[0]));
        $section = ucfirst($admin->getAlias());
        $controller = $bundle . ':' . $section . ':' . $action;
        try {
            $controller = $this->parser->parse($controller);
        }
        catch(\InvalidArgumentException $e) {
            $controller = $this->parser->parse('SnowcapAdminBundle:Content:' . $action);
        }

        return new Route($pattern, array('_controller' => $controller, 'alias' => $admin->getAlias()));
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