<?php

namespace Snowcap\AdminBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Snowcap\CoreBundle\Navigation\NavigationRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

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
     * @param Request $request
     * @param ConfigurationInterface $configuration
     * @return bool|void
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
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
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
     * @return bool
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return in_array('Snowcap\AdminBundle\Admin\AdminInterface', class_implements($configuration->getClass()));
    }
}