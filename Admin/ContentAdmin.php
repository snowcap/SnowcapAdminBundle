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
     * Each field in the form will be used to modify the querybuilder returned
     * by the getQueryBuilder() method of this class and thus must be named
     * accordingly
     *
     * @return \Symfony\Component\Form\Form
     */
    public function getSearchForm()
    {
        return null;
    }

    /**
     * Return the admin filter form for this content (used in the list view)
     *
     * @return \Symfony\Component\Form\Form
     */
    public function getFilterForm()
    {
        return null;
    }

    /**
     * Return the main admin querybuilder for this content
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
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
     * Find the entity with the given identifier
     *
     * @param mixed $entityId
     * @return object
     */
    public function findEntity($entityId)
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        $entity = $em->getRepository($this->getParam('entity_class'))->find($entityId);
        return $entity;
    }

    /**
     * Save the entity in the database
     *
     * @param $entity
     */
    public function saveEntity($entity)
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->persist($entity);
    }

    public function flush()
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->flush();
    }

    /**
     * Deletes the entity with the given identifier
     *
     * @param object $entity
     */
    public function deleteEntity($entity)
    {
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->remove($entity);
    }

    /**
     * Determine if the admin is translatable - false in this case, to be overridden
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return false;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function prePersist(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function preUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function postRemove(\Doctrine\ORM\Event\LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs) {}

    /**
     * @param PreFlushEventArgs $ea
     * @param $entities
     */
    public function preFlush($ea, $entities) {}
}