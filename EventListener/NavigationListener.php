<?php

namespace Snowcap\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Snowcap\CoreBundle\Navigation\NavigationRegistry;

class NavigationListener {
    /**
     * @var \Snowcap\CoreBundle\Navigation\NavigationRegistry
     */
    private $registry;

    /**
     * @param \Snowcap\CoreBundle\Navigation\NavigationRegistry $registry
     */
    public function __construct(NavigationRegistry $registry)
    {
        $this->registry = $registry;
    }
    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event) {
        $request = $event->getRequest();
        if($request->attributes->has('admin')) {
            $this->registry->addActivePath($request->attributes->get('admin')->getDefaultPath());
        }
    }
}