<?php

namespace Snowcap\AdminBundle\Admin;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\User\UserInterface;

interface AdminInterface
{
    /**
     * @return string
     */
    public function getDefaultPath();

    /**
     * @param RouteCollection $routeCollection
     */
    public function addRoutes(RouteCollection $routeCollection);

    /**
     * @param string $alias
     */
    public function setAlias($alias);

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * @param $name
     * @return mixed
     */
    public function hasOption($name);

    /**
     * Should return one of VoterInterface::ACCESS_XXX constant (1, 0 or -1)
     *
     * @param UserInterface $user
     * @param string $attribute
     * @param mixed $object
     * @return int
     */
    public function isGranted(UserInterface $user, $attribute, $object);
}