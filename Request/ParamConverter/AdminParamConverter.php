<?php

namespace Snowcap\AdminBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
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
     * @param \Snowcap\AdminBundle\AdminManager $adminManager
     */
    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration
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