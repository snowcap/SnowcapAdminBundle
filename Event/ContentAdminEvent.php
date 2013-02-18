<?php

namespace Snowcap\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Snowcap\AdminBundle\Admin\ContentAdmin;

class ContentAdminEvent extends Event
{
    /**
     * @var Admin\ContentAdmin
     */
    private $admin;

    /**
     * @var object
     */
    private $entity;

    /**
     * @param Admin\ContentAdmin $admin
     * @param object $entity
     */
    public function __construct(ContentAdmin $admin, $entity)
    {
        $this->admin = $admin;
        $this->entity = $entity;
    }

    /**
     * @return \Snowcap\AdminBundle\Admin\ContentAdmin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}