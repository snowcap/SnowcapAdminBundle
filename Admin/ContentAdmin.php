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
        $queryBuilder = $this->environment->get('doctrine')->getEntityManager()->createQueryBuilder();
        $this->configureContentQueryBuilder($queryBuilder);
        $grid->setQueryBuilder($queryBuilder);
        $this->configureContentGrid($grid);
        return $grid;
    }

    /**
     * @return mixed
     * @throws \Snowcap\AdminBundle\Exception
     */
    public function getContentType(){
        if (!array_key_exists('content_type_class', $this->params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "content_type_class" parameter or override the getContentType() method', $this->getCode()), Exception::SECTION_INVALID);
        }
        elseif (!class_exists($this->params['content_type_class'])) {
            throw new Exception(sprintf('The admin section %s has an invalid "content_type_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
        return new $this->params['content_type_class'];
    }

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


}