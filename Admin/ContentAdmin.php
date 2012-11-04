<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Util\PropertyPath;
use Snowcap\AdminBundle\Datalist\DatalistFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouteCollection;

use Snowcap\AdminBundle\Exception;
use Snowcap\CoreBundle\Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\Routing\Route;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class ContentAdmin extends AbstractAdmin
{
    const SAVEMODE_NORMAL = 'normal';
    const SAVEMODE_CONTINUE = 'continue';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var DatalistFactory
     */
    protected $datalistFactory;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var ContentRoutingHelper
     */
    protected $routingHelper;

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
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('e')
            ->from($this->getEntityClass(), 'e');
        return $queryBuilder;
    }

    /**
     * Instantiate and return a blank entity
     *
     * @return mixed
     */
    public function buildEntity()
    {
        $entityClassName = $this->getEntityClass();
        return new $entityClassName;
    }

    /**
     * Find the entity with the given identifier
     *
     * @param mixed $entityId
     * @return object
     */
    public function findEntity($entityId)
    {
        $entity = $this->em->getRepository($this->getEntityClass())->find($entityId);
        return $entity;
    }

    /**
     * Save the entity in the database
     *
     * @param $entity
     */
    public function saveEntity($entity)
    {
        $this->em->persist($entity);
    }

    public function flush()
    {
        $this->em->flush();
    }

    /**
     * Deletes the entity with the given identifier
     *
     * @param object $entity
     */
    public function deleteEntity($entity)
    {
        $this->em->remove($entity);
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
        $datalist = $this->datalistFactory->create($view, $name);
        $datalist->setQueryBuilder($this->getQueryBuilder());
        $datalist->addAction(
            $this->routingHelper->getRouteName($this, 'update'),
            array('alias' => $this->getAlias()),
            array('label' => 'content.actions.edit', 'icon' => 'icon-edit')
        );
        $datalist->addAction(
            $this->routingHelper->getRouteName($this, 'delete'),
            array('alias' => $this->getAlias()),
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
        return $this->formFactory->createNamedBuilder('form', 'search', null, array('virtual' => true));
    }

    /**
     * Create a pre-configured filter form builder
     *
     * @return FormBuilder
     */
    protected function createFilterFormBuilder()
    {
        return $this->formFactory->createNamedBuilder('form', 'filters', null, array('virtual' => true));
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

    public function toString($entity)
    {
        $path = $this->toStringPath();

        if($path === null) {
            return null;
        }

        $propertyPath = new PropertyPath($path);
        $output = $propertyPath->getValue($entity);

        return $output;
    }

    abstract public function getEntityClass();

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistFactory $datalistFactory
     */
    public function setDatalistFactory(DatalistFactory $datalistFactory)
    {
        $this->datalistFactory = $datalistFactory;
    }

    /**
     * @param \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper $routingHelper
     */
    public function setRoutingHelper(ContentRoutingHelper $routingHelper)
    {
        $this->routingHelper = $routingHelper;
    }

    /**
     * @param string $alias
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     */
    public function addRoutes(RouteCollection $routeCollection)
    {
        // Add index route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'index'),
            $this->routingHelper->getRoute($this, 'index', array(), true)
        );
        // Add create route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'create'),
            $this->routingHelper->getRoute($this, 'create')
        );
        // Add update route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'update'),
            $this->routingHelper->getRoute($this, 'update', array('id'))
        );
        // Add delete route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'delete'),
            $this->routingHelper->getRoute($this, 'delete', array('id'))
        );
    }
}