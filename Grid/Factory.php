<?php
namespace Snowcap\AdminBundle\Grid;

class Factory {

    protected $types = array(
        'content' => '\\Snowcap\\AdminBundle\\Grid\\Content',
        'orderablecontent' => '\\Snowcap\\AdminBundle\\Grid\\OrderableContent',
    );
    protected $formFactory;

    public function __construct($formFactory){
        $this->formFactory = $formFactory;
    }

    public function create($type)
    {
        $gridClass = $this->types[$type];
        return $this->createFromClass($gridClass);
    }

    public function createFromClass($gridClass) {
        $grid = new $gridClass();
        if(is_callable(array($grid, 'setFormFactory'))) {
            call_user_func(array($grid, 'setFormFactory'), $this->formFactory);
        }
        return $grid;
    }
}