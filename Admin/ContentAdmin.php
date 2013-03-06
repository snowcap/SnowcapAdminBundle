<?php
namespace Snowcap\AdminBundle\Admin;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Routing\RouteCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Snowcap\AdminBundle\Datalist\DatalistFactory;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;
use Snowcap\AdminBundle\Event\AdminEvents;
use Snowcap\AdminBundle\Event\ContentAdminEvent;

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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

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
            ->from($this->getEntityClass(), 'e')
            ->orderBy('e.id', 'DESC');
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
        if($this->em->getUnitOfWork()->isInIdentityMap($entity)) {
            $this->em->flush();
            $this->eventDispatcher->dispatch(AdminEvents::CONTENT_UPDATE, new ContentAdminEvent($this, $entity));
        }
        else {
            $this->em->persist($entity);
            $this->em->flush();
            $this->eventDispatcher->dispatch(AdminEvents::CONTENT_CREATE, new ContentAdminEvent($this, $entity));
        }
    }

    /**
     * Deletes the entity with the given identifier
     *
     * @param object $entity
     */
    public function deleteEntity($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();

        $this->eventDispatcher->dispatch(AdminEvents::CONTENT_DELETE, new ContentAdminEvent($this, $entity));
    }

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
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
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
        // Add view route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'view'),
            $this->routingHelper->getRoute($this, 'view', array('id'))
        );
        // Add create route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'create'),
            $this->routingHelper->getRoute($this, 'create')
        );
        // Add modal create route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'modalCreate'),
            $this->routingHelper->getRoute($this, 'modalCreate')
        );
        // Add autocomplete list route
        $routeCollection->add(
            $this->routingHelper->getRouteName($this, 'autocompleteList'),
            $this->routingHelper->getRoute($this, 'autocompleteList', array('where', 'property', 'query'))
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

    /**
     * @return string
     */
    public function getDefaultPath()
    {
        return $this->routingHelper->generateUrl($this, 'index');
    }

    /**
     * @return string
     */
    abstract public function getEntityClass();

    /**
     * Return the main admin form for this content
     *
     * @param object $data
     * @return \Symfony\Component\Form\Form
     */
    abstract public function getForm();

    /**
     * Return the main admin list for this content
     *
     * @return \Snowcap\AdminBundle\Datalist\DatalistInterface
     */
    abstract public function getDatalist();

    /**
     * @param mixed $entity
     * @return string
     */
    abstract public function getEntityName($entity);
}