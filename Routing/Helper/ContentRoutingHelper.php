<?php

namespace Snowcap\AdminBundle\Routing\Helper;

use Snowcap\CoreBundle\Util\String;
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

    public function getRoute(ContentAdmin $admin, $action, $params = array(), $defaultRoute = false)
    {
        $defaults = array();
        $pattern = '/' . $admin->getAlias();
        if(!$defaultRoute) {
            $pattern .= '/' . $action;
        }
        foreach($params as $paramKey => $paramValue) {
            if(is_int($paramKey)) {
                $paramName = $paramValue;
            }
            else {
                $paramName = $paramKey;
                $defaults[$paramName] = $paramValue;
            }

            $pattern .= '/{' . $paramName . '}';
        }

        preg_match('/(?:[A-Z](?:[A-Za-z0-9])+\\\)*(?:)[A-Z](?:[A-Za-z0-9])+Bundle/', get_class($admin), $matches);
        $bundle = implode('', explode('\\', $matches[0]));
        $section = String::camelize($admin->getAlias());
        $controller = $bundle . ':' . $section . ':' . $action;
        try {
            $controller = $this->parser->parse($controller);
        }
        catch(\InvalidArgumentException $e) {
            $controller = $this->parser->parse('SnowcapAdminBundle:Content:' . $action);
        }

        $defaults = array_merge(
            array('_controller' => $controller, 'alias' => $admin->getAlias()),
            $defaults
        );
        return new Route($pattern, $defaults);
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
