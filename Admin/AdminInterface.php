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

    /**
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * @param $name
     * @return mixed
     */
    public function hasOption($name);
}