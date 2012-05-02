<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;

use Snowcap\AdminBundle\Exception;
use Snowcap\CoreBundle\Doctrine\ORM\Event\PreFlushEventArgs;

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
     * Create a datalist with the provided view mode and name
     *
     * @param string $view a registered grid view mode
     * @param string $name
     * @return \Snowcap\AdminBundle\Datalist\AbstractDatalist
     */
    protected function createDatalist($view, $name)
    {
        $datalist = $this->environment->get('snowcap_admin.datalist_factory')->create($view, $name);
        $datalist->setQueryBuilder($this->getQueryBuilder());
        $datalist->addAction(
            'snowcap_admin_content_update',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.edit', 'icon' => 'icon-edit')
        );
        $datalist->addAction(
            'snowcap_admin_content_delete',
            array('code' => $this->getCode()),
            array(
                'label' => 'content.actions.delete.label',
                'icon' => 'icon-remove',
                'confirm' => true,
                'confirm_title' => 'content.actions.delete.confirm.title',
                'confirm_body' => 'content.actions.delete.confirm.body',
            )
        );
        return $datalist;
    }

    /**
     * Create a pre-configured search form builder
     *
     * @return FormBuilder
     */
    protected function createSearchFormBuilder()
    {
        return $this->environment->get('form.factory')->createNamedBuilder('form', 'search', null, array('virtual' => true));
    }

    /**
     * Create a pre-configured filter form builder
     *
     * @return FormBuilder
     */
    protected function createFilterFormBuilder()
    {
        return $this->environment->get('form.factory')->createNamedBuilder('form', 'filters', null, array('virtual' => true));
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function prePersist(LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function postPersist(LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function preUpdate(LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function postUpdate(LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $ea
     * @param $entity
     */
    public function postRemove(LifecycleEventArgs $ea, $entity) {}

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs) {}

    /**
     * @param \Snowcap\CoreBundle\Doctrine\ORM\Event\PreFlushEventArgs $ea
     * @param array $entities
     */
    public function preFlush(PreFlushEventArgs $ea, $entities) {}
}