<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactory;

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
        $grid->addAction('content_update', array('code' => $this->getCode()));
        $queryBuilder = $this->environment->get('doctrine')->getEntityManager()->createQueryBuilder(); /* @var QueryBuilder $queryBuilder */
        $this->configureListQueryBuilder($queryBuilder);
        $grid->setQueryBuilder($queryBuilder);
        $this->configureListGrid($grid);
        return $grid;
    }

    /**
     * @abstract
     * @param \Snowcap\AdminBundle\Grid\ContentGrid $grid
     */
    abstract protected function configureListGrid(ContentGrid $grid);

    protected function configureListQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->select('e')
            ->from($this->getParam('entity_class'), 'e');
    }


    public function validateParams(array $params)
    {
        parent::validateParams($params);
        if (!array_key_exists('entity_class', $params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "entity_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
        elseif (!class_exists($params['entity_class'])) {
            throw new Exception(sprintf('The admin section %s has an invalid "entity_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
    }


    /**
     * Generate the default url for the admin instance
     *
     * @return string
     */
    public function getDefaulturl()
    {
        return $this->environment->get('router')->generate('snowcap_admin_content_index', array('code' => $this->getCode()));
    }

    public function getCreateRoute()
    {
        return $this->environment->buildRoute('content_create', array('code' => $this->code));
    }

    public function getUpdateRoute($entity)
    {
        return $this->environment->buildRoute('content_update', array('code' => $this->code, 'id' => $entity->getId()));
    }


}