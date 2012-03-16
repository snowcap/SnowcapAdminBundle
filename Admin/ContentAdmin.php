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

    protected function createDatalist($name, $type)
    {
        $datalist = $this->environment->get('snowcap_admin.datalist_factory')->createDatalist($name, $type);
        $datalist->setQueryBuilder($this->getQueryBuilder());
        $datalist->addAction(
            'snowcap_admin_content_update',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.edit', 'icon' => 'icon-edit')
        );
        $datalist->addAction(
            'snowcap_admin_content_delete',
            array('code' => $this->getCode()),
            array('label' => 'content.actions.delete', 'icon' => 'icon-remove')
        );

        return $datalist;
    }
    /**
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


    public function getSearchForm()
    {
        return null;
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

    /**
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
     * @param mixed $entityId
     */
    public function deleteEntity($entityId)
    {
        $entity = $this->findEntity($entityId);
        $em = $this->environment->get('doctrine')->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    /**
     * @return array
     */
    public function getFieldsets()
    {
        return array();
    }

    /**
     * @return string
     */
    public function getPreviewBlockName()
    {
        return 'default_preview';
    }

}