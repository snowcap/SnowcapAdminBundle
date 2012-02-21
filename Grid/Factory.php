<?php
namespace Snowcap\AdminBundle\Grid;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use Symfony\Bundle\DoctrineBundle\Registry;
use Snowcap\AdminBundle\Admin\Base;

class Factory {
    /**
     * @var array
     */
    protected $types = array(
        'content' => '\\Snowcap\\AdminBundle\\Grid\\ContentGrid',
        'orderablecontent' => '\\Snowcap\\AdminBundle\\Grid\\OrderableContent',
    );
    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $formFactory;
    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;
    /**
     * @var \Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;
    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory) {
        $this->formFactory = $formFactory;
    }
    /**
     * @param \Symfony\Component\Routing\Router $router
     */
    public function setRouter(Router $router) {
        $this->router = $router;
    }
    /**
     * @param \Symfony\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine) {
        $this->doctrine = $doctrine;
    }



    public function create($type, $name)
    {
        $gridClass = $this->types[$type];
        $grid = new $gridClass($name);
        if(is_callable(array($grid, 'setFormFactory'))) {
            call_user_func(array($grid, 'setFormFactory'), $this->formFactory);
        }
        if(is_callable(array($grid, 'setRouter'))) {
            call_user_func(array($grid, 'setRouter'), $this->router);
        }
        if(is_callable(array($grid, 'setQueryBuilder'))) {
            call_user_func(array($grid, 'setQueryBuilder'), $this->doctrine->getEntityManager()->createQueryBuilder());
        }
        return $grid;
    }
}