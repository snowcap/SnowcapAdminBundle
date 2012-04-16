<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;

use Snowcap\AdminBundle\Exception;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class ContentAdmin extends AbstractAdmin
{
    const SAVEMODE_NORMAL = 'normal';
    const SAVEMODE_CONTINUE = 'continue';

    public function getDefaultRoute()
    {
        return 'snowcap_admin_content_index';
    }

    /**
     * Return the main admin form for this content
     *
     * @param object $data
     * @return \Symfony\Component\Form\Form
     */
    abstract public function getForm($data = null);

    /**
     * Return the main admin list for this content
     *
     * @return \Snowcap\AdminBundle\Datalist\AbstractDatalist
     */
    abstract public function getDatalist();

    /**
     * Return the admin search form for this content (used in the list view)
     *
     * @return \Symfony\Component\Form\Form
     */
    public function getSearchForm()
    {
        return null;
    }

    /**
     * Return the main admin querybuilder for this content
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $queryBuilder = $this->environment->get('doctrine')->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('e')
            ->from($this->getParam('entity_class'), 'e');
        return $queryBuilder;
    }

    /**
     * Validate the admin section params
     *
     * @param array $params
     * @throws \Snowcap\AdminBundle\Exception
     */
    public function validateParams(array $params)
    {
        parent::validateParams($params);
        // Checks that there is a valid entity class in the config
        if (!array_key_exists('entity_class', $params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "entity_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
        elseif (!class_exists($params['entity_class'])) {
            throw new Exception(sprintf('The admin section %s has an invalid "entity_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
    }

    /**
     * Instantiate and return a blank entity
     *
     * @return mixed
     */
    public function buildEntity()
    {
        $entityName = $this->getParam('entity_class');
        return new $entityName;
    }

    /**
     * Find the entty with the given identifier
     *
     * @param mixed $entityId
     * @return mixed
     */
    public function findEntity($entityId)
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        $entity = $em->getRepository($this->getParam('entity_class'))->find($entityId);
        return $entity;
    }

    /**
     * Save an entity in the database
     *
     * @param $entity
     */
    public function saveEntity($entity)
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Deletes the entity with the given identifier
     *
     * @param mixed $entityId
     */
    public function deleteEntity($entityId)
    {
        $entity = $this->findEntity($entityId);
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function isTranslatable()
    {
        return false;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param Entity instance $entity
     * @return bool
     */
    public function prePersist(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity)
    {
        return true;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param Entity instance $entity
     * @return bool
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity)
    {
        return true;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param Entity instance $entity
     * @return bool
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity)
    {
        return true;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param Entity instance $entity
     * @return bool
     */
    public function postRemove(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity)
    {
        return true;
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs
     * @return bool
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        return true;
    }

    /**
     * @param PreFlushEventArgs $ea
     * @param Array of entities instances $entities
     * @return bool
     */
    public function preFlush($ea, $entities)
    {
        return true;
    }
}