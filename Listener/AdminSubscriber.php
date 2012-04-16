<?php

namespace Snowcap\AdminBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

use Snowcap\CoreBundle\Doctrine\ORM\Event\PreFlushEventArgs;
use Snowcap\AdminBundle\Environment;

class AdminSubscriber implements EventSubscriber
{

    /**
     * @var Snowcap\AdminBundle\Environment
     */
    protected $adminEnvironment;

    protected $entityMapping = array();

    /**
     * @param \Snowcap\AdminBundle\Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->adminEnvironment = $environment;

        $admins = $this->adminEnvironment->getAdmins();
        foreach($admins as $admin) {
            $this->entityMapping[$admin->getParam('entity_class')] = $admin;
        }
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array('prePersist', 'postPersist', 'postUpdate', 'postRemove','loadClassMetadata','preFlush');
    }

    public function prePersist($ea) {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('prePersist', $entity, array($ea, $entity));
    }

    public function postPersist($ea) {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postPersist', $entity, array($ea, $entity));
    }

    public function postUpdate($ea) {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postUpdate', $entity, array($ea, $entity));
    }

    public function postRemove($ea) {
        $entity = $ea->getEntity();
        $this->callEventOnAdmin('postRemove', $entity, array($ea, $entity));
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $meta = $eventArgs->getClassMetadata();
        $key = $meta->getName();
        if(array_key_exists($key, $this->entityMapping)) {
            call_user_func_array( array($this->entityMapping[$key], 'loadClassMetadata'), array($eventArgs));
        }
    }

    public function preFlush(PreFlushEventArgs $ea)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $ea->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();

        $entitiesToProcess = array();
        $keyToProcess = false;
        $entityMaps = $unitOfWork->getIdentityMap();
        foreach ($entityMaps as $entities) {
            foreach ($entities as $entity) {
                $key = get_class($entity);
                if(array_key_exists($key, $this->entityMapping)) {
                    $entitiesToProcess[] = $entity;
                    $keyToProcess = $key;
                }
            }
        }
        if(count($entitiesToProcess) > 0) {
            call_user_func_array( array($this->entityMapping[$keyToProcess], 'preFlush'), array($ea, $entitiesToProcess));
        }
    }

    private function callEventOnAdmin($event, $entity, $arguments)
    {
        $key = get_class($entity);
        if(array_key_exists($key, $this->entityMapping)) {
            call_user_func_array( array($this->entityMapping[$key], $event), $arguments);
        }
    }
}