<?php

namespace Snowcap\AdminBundle\Admin;

use Symfony\Component\Routing\RouteCollection;

interface AdminInterface
{
    /**
     * Return the admin code
     *
     * @return string
     */
    public function getCode();

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
     * @return string
     */
    public function getName();
}