<?php

namespace Snowcap\AdminBundle\Admin;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class AbstractAdmin implements AdminInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name)
    {
        if(!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option with name "%s" does not exist', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $data
     * @param array $options
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createBuilder('form', $data, $options);
    }

    /**
     * @param string $type
     * @param mixed $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * @param array $options
     * @return \Snowcap\AdminBundle\Datalist\DatalistBuilder
     */
    public function createDatalistBuilder(array $options = array())
    {
        return $this->container->get('snowcap_admin.datalist_factory')->createBuilder('datalist', $data, $options);
    }

    /**
     * @param string $type
     * @param array $options
     * @return \Snowcap\AdminBundle\Datalist\DatalistInterface
     */
    public function createDatalist($type, array $options = array())
    {
        return $this->container->get('snowcap_admin.datalist_factory')->create($type, $options);
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * By default, grant access
     *
     * @param UserInterface $user
     * @param string $attribute
     * @param mixed $object
     * @return int
     */
    public function isGranted(UserInterface $user, $attribute, $object)
    {
        return VoterInterface::ACCESS_GRANTED;
    }
}