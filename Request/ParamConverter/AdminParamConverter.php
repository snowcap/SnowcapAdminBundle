<?php

namespace Snowcap\AdminBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Snowcap\CoreBundle\Navigation\NavigationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Snowcap\AdminBundle\AdminManager;

class AdminParamConverter implements ParamConverterInterface {
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @var \Snowcap\CoreBundle\Navigation\NavigationRegistry
     */
    private $registry;

    /**
     * @param AdminManager $adminManager
     * @param NavigationRegistry $registry
     */
    public function __construct(AdminManager $adminManager, NavigationRegistry $registry)
    {
        $this->adminManager = $adminManager;
        $this->registry = $registry;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return bool|void
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();
        $alias = $request->attributes->get('alias');
        if(!$request->attributes->has('alias')) {
            throw new NotFoundHttpException('Cannot find admin without alias');
        }
        try {
            $admin = $this->adminManager->getAdmin($alias);
            $request->attributes->set($param, $admin);
            $this->registry->addActivePath($admin->getDefaultPath());
        }
        catch(\InvalidArgumentException $e)
        {
            throw new NotFoundHttpException(sprintf('Cannot find admin with alias "%s"', $alias));
        }
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array('Snowcap\AdminBundle\Admin\AdminInterface', class_implements($configuration->getClass()));
    }
}