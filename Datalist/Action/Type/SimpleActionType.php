<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;

class SimpleActionType extends AbstractActionType {
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array('route_params' => array()))
            ->setRequired(array('route'));
    }

    public function getUrl(DatalistActionInterface $action, $item, array $options = array())
    {
        $parameters = array();
        foreach($options['route_params'] as $paramName => $paramPath) {
            $propertyPath = new PropertyPath($paramPath);
            $paramValue = $propertyPath->getValue($item);
            $parameters[$paramName] = $paramValue;
        }

        return $this->router->generate($options['route'], $parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simple';
    }
}