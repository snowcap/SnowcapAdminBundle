<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SimpleActionType
 * @package Snowcap\AdminBundle\Datalist\Action\Type
 */
class SimpleActionType extends AbstractActionType
{
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

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
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($options['params'] as $paramName => $paramPath) {
            $paramValue = $accessor->getValue($item, $paramPath);
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