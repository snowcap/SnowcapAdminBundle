<?php
namespace Snowcap\AdminBundle\Admin;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouteCollection;

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
     * @param OptionsResolverInterface $resolver
     *
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setOptional(array('entity_class', 'entity_name'))
            ->setAllowedTypes(array(
                'entity_class' => 'string',
                'entity_name' => 'string'
            ));
    }

    /**
     * Return the main admin querybuilder for this content
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
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
        $entity = $this->getEntityManager()->getRepository($this->getEntityClass())->find($entityId);

        return $entity;
    }

    /**
     * Save the entity in the database
     *
     * @param $entity
     */
    public function saveEntity($entity)
    {
        if($this->getEntityManager()->getUnitOfWork()->isInIdentityMap($entity)) {
            $this->getEntityManager()->flush();
            $this->getEventDispatcher()->dispatch(AdminEvents::CONTENT_UPDATE, new ContentAdminEvent($this, $entity));
        }
        else {
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
            $this->getEventDispatcher()->dispatch(AdminEvents::CONTENT_CREATE, new ContentAdminEvent($this, $entity));
        }
    }

    /**
     * Deletes the entity with the given identifier
     *
     * @param object $entity
     */
    public function deleteEntity($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        $this->getEventDispatcher()->dispatch(AdminEvents::CONTENT_DELETE, new ContentAdminEvent($this, $entity));
    }

    /**
     * @param string $alias
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     */
    public function addRoutes(RouteCollection $routeCollection)
    {
        // Add index route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'index'),
            $this->getRoutingHelper()->getRoute($this, 'index', array(), true)
        );
        // Add view route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'view'),
            $this->getRoutingHelper()->getRoute($this, 'view', array('id'))
        );
        // Add create route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'create'),
            $this->getRoutingHelper()->getRoute($this, 'create')
        );
        // Add modal create route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'modalCreate'),
            $this->getRoutingHelper()->getRoute($this, 'modalCreate')
        );
        // Add autocomplete list route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'autocompleteList'),
            $this->getRoutingHelper()->getRoute($this, 'autocompleteList', array('where', 'id_property', 'property', 'query'))
        );
        // Add update route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'update'),
            $this->getRoutingHelper()->getRoute($this, 'update', array('id'))
        );
        // Add modal update route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'modalUpdate'),
            $this->getRoutingHelper()->getRoute($this, 'modalUpdate', array('id'))
        );
        // Add delete route
        $routeCollection->add(
            $this->getRoutingHelper()->getRouteName($this, 'delete'),
            $this->getRoutingHelper()->getRoute($this, 'delete', array('id'))
        );
    }

    /**
     * @return string
     */
    public function getDefaultPath()
    {
        return $this->getRoutingHelper()->generateUrl($this, 'index');
    }

    /**
     * @return \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    public function getRoutingHelper()
    {
        return $this->container->get('snowcap_admin.routing_helper_content');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return string
     */
    public function getEntityClass(){
        if(!$this->hasOption('entity_class')) {
            throw new InvalidOptionsException('The option "entity_class" must be set (or you have to define a getEntityClass method)');
        };

        return $this->getOption('entity_class');
    }

    /**
     * Return the main admin form for this content
     *
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
     * @param object $entity
     *
     * @return string
     */
    public function getEntityName($entity) {
        if(!$this->hasOption('entity_name')) {
            throw new InvalidOptionsException('The option "entity_name" must be set (or you have to define a getEntityName method)');
        };

        $accessor = PropertyAccess::getPropertyAccessor();

        return $accessor->getValue($entity, $this->getOption('entity_name'));
    }
}
