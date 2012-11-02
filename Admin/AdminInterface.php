<?php

namespace Snowcap\AdminBundle\Admin;

use Symfony\Component\Routing\RouteCollection;

interface AdminInterface
{
    /**
     * @return string
     */
    public function getDefaultUrl();

    /**
     * @param string $alias
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     */
    public function addRoutes($alias, RouteCollection $routeCollection);

    /**
     * @param string $alias
     */
    public function setAlias($alias);

    /**
     * @return string
     */
    public function getAlias();
}