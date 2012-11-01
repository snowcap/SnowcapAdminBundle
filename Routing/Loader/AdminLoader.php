<?php

namespace Snowcap\AdminBundle\Routing\Loader;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

use Snowcap\AdminBundle\AdminManager;

class AdminLoader implements LoaderInterface {
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    private $adminManager;
    /**
     * @param \Snowcap\AdminBundle\AdminManager $adminManager
     */
    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

    /**
     * Loads a resource.
     *
     * @param mixed  $resource The resource
     * @param string $type     The resource type
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        foreach($this->adminManager->getAdmins() as $alias => $admin) {
            $admin->addRoutes($alias, $routes);
        }

        return $routes;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'extra' === $type;
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolverInterface A LoaderResolverInterface instance
     */
    public function getResolver()
    {
        // TODO: Implement getResolver() method.
    }

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolverInterface $resolver A LoaderResolverInterface instance
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // TODO: Implement setResolver() method.
    }
}