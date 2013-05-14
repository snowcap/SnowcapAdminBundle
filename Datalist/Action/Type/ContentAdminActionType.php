<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\AdminManager;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;
use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;

class ContentAdminActionType extends AbstractActionType {
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    protected $adminManager;

    /**
     * @var \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    protected $routingHelper;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(AdminManager $adminManager, ContentRoutingHelper $routingHelper)
    {
        $this->adminManager = $adminManager;
        $this->routingHelper = $routingHelper;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $adminManager = $this->adminManager;
        $adminNormalizer = function(Options $options, $admin) use($adminManager) {
            if(!$admin instanceof ContentAdmin) { //TODO: deprecated notice ?
                $admin = $adminManager->getAdmin($admin);
            }

            return $admin;
        };

        $resolver
            ->setDefaults(array(
                'params' => array('id' => 'id'),
                'modal' => false,
            ))
            ->setOptional(array('icon'))
            ->setRequired(array('admin', 'action'))
            ->setAllowedTypes(array(
                'params' => 'array',
                'admin' => array('string', 'Snowcap\AdminBundle\Admin\ContentAdmin'),
                'action' => 'string',
            ))
            ->setNormalizers(array(
                'admin' => $adminNormalizer
            ));
    }

    public function getUrl(DatalistActionInterface $action, $item, array $options = array())
    {
        $parameters = array();
        $accessor = PropertyAccess::getPropertyAccessor();
        foreach($options['params'] as $paramName => $paramPath) {
            $paramValue = $accessor->getValue($item, $paramPath);
            $parameters[$paramName] = $paramValue;
        }

        return $this->routingHelper->generateUrl($options['admin'], $options['action'], $parameters);
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param $item
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistActionInterface $action, $item, array $options)
    {
        parent::buildViewContext($viewContext, $action, $item, $options);

        if(true === $options['modal']) {
            $viewContext['attr']['data-bootstrap'] = 'modal';
        }

        if(isset($options['icon'])) {
            $viewContext['icon'] = $options['icon'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_admin';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'simple';
    }
}