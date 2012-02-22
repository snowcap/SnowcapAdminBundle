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

    public function create($type, $name)
    {
        $gridClass = $this->types[$type];
        $grid = new $gridClass($name);
        return $grid;
    }
}