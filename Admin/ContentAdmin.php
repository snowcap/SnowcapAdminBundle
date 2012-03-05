<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;

use Snowcap\AdminBundle\Form\ContentType;
use Snowcap\AdminBundle\Grid\ContentGrid;
use Snowcap\AdminBundle\Exception;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class ContentAdmin extends AbstractAdmin
{
    public function getDefaultPath()
    {
        return $this->environment->get('router')->generate('content', array('code' => $this->getCode()));
    }

    protected function getDefaultParams()
    {
        return array('grid_type' => 'content');
    }

    /**
     * Return the main content grid used to display the entity listing
     *
     * @return \Snowcap\AdminBundle\Grid\ContentGrid
     */
    public function getContentGrid()
    {
        $grid = $this->environment->get('snowcap_admin.grid_factory')->create($this->getParam('grid_type'), $this->getCode());
        $grid->addAction(
            'snowcap_admin_content_update',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.edit', 'icon' => 'icon-edit')
        );
        $grid->addAction(
            'snowcap_admin_content_delete',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.delete', 'icon' => 'icon-remove')
        );
        $queryBuilder = $this->environment->get('doctrine')->getEntityManager()->createQueryBuilder();
        $this->configureContentQueryBuilder($queryBuilder);
        $grid->setQueryBuilder($queryBuilder);
        $this->configureContentGrid($grid);
        return $grid;
    }

    /**
     * @param $data
     * @return \Symfony\Component\Form\Form
     */
    public function getForm($data)
    {
        $builder = $this->environment->get('form.factory')->createBuilder('form', $data, array('data_class' => $this->getParam('entity_class')));
        $this->buildForm($builder);
        return $builder->getForm();
    }

    abstract protected function buildForm(FormBuilder $builder);

    /**
     * Configure the main listing grid
     *
     * @abstract
     * @param \Snowcap\AdminBundle\Grid\ContentGrid $grid
     */
    abstract protected function configureContentGrid(ContentGrid $grid);

    /**
     * Configure the main listing query builder
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function configureContentQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->select('e')
            ->from($this->getParam('entity_class'), 'e');
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
    public function getBlankEntity()
    {
        $entityName = $this->getParam('entity_class');
        return new $entityName;
    }

    public function findEntity($entityId){
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

    public function deleteEntity($entityId)
    {
        $entity = $this->findEntity($entityId);
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function getFieldsets()
    {
        return array();
    }

}