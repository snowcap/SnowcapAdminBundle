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
    protected $router;

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
            ->setDefaults(array(
                'params' => array(),
            ))
            ->setRequired(array('route'));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param object $item
     * @param array $options
     * @return string
     */
    public function getUrl(DatalistActionInterface $action, $item, array $options = array())
    {
        $parameters = array();
        foreach($options['params'] as $paramName => $paramPath) {
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

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'simple';
    }
}