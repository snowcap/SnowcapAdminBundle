<?php

namespace Snowcap\AdminBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Snowcap\AdminBundle\AdminManager;

class AdminSubscriber implements EventSubscriber
{
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    protected $adminManager;

    /**
     * @var array
     */
    protected $entityMapping = array();

    /**
     * @param \Snowcap\AdminBundle\AdminManager $environment
     */
    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;

        $admins = $this->adminManager->getAdmins();
        foreach ($admins as $admin) {
            if($admin instanceof \Snowcap\AdminBundle\Admin\ContentAdmin) {
                $this->entityMapping[$admin->getParam('entity_class')] = $admin;
            }
        }
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'preRemove',
            'postRemove',
            'prePersist',
            'postPersist',
            'preUpdate',
            'postUpdate',
            'postLoad',
        );
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function preRemove(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('preRemove', $entity, array($ea, $entity));
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function postRemove(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postRemove', $entity, array($ea, $entity));
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function prePersist(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('prePersist', $entity, array($ea, $entity));
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function postPersist(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postPersist', $entity, array($ea, $entity));
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function preUpdate(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('preUpdate', $entity, array($ea, $entity));
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function postUpdate(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postUpdate', $entity, array($ea, $entity));
    }

    /**
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $ea
     */
    public function postLoad(LifecycleEventArgs $ea)
    {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postLoad', $entity, array($ea, $entity));
    }

    /**
     * @param string $eventName
     * @param object $entity
     * @param array $arguments
     */
    private function callEventOnAdmin($eventName, $entity, array $arguments)
    {
        $key = get_class($entity);
        if (array_key_exists($key, $this->entityMapping)) {
            call_user_func_array(array($this->entityMapping[$key], $eventName), $arguments);
        }
    }
}