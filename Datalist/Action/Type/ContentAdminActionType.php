<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;

use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;

class ContentAdminActionType extends AbstractActionType {
    /**
     * @var \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    private $routingHelper;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(ContentRoutingHelper $routingHelper)
    {
        $this->routingHelper = $routingHelper;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array('params' => array('id' => 'id')))
            ->setRequired(array('admin', 'action'))
            ->setAllowedTypes(array(
                'params' => 'array',
                'admin' => 'Snowcap\AdminBundle\Admin\ContentAdmin',
                'action' => 'string'
            ));
    }

    public function getUrl(DatalistActionInterface $action, $item, array $options = array())
    {
        $parameters = array();
        foreach($options['params'] as $paramName => $paramPath) {
            $propertyPath = new PropertyPath($paramPath);
            $paramValue = $propertyPath->getValue($item);
            $parameters[$paramName] = $paramValue;
        }

        return $this->routingHelper->generateUrl($options['admin'], $options['action'], $parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simple';
    }
}