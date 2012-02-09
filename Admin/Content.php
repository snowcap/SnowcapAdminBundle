<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder,
Symfony\Component\Form\AbstractType,
Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Form\ContentType,
    Snowcap\AdminBundle\Grid\Content as ContentGrid,
    Snowcap\AdminBundle\Exception;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class Content extends Base
{
    public function getDefaultPath() {
        return $this->environment->get('router')->generate('content', array('code' => $this->getCode()));
    }

    protected function getDefaultParams() {
        return array(
            'grid_type' => 'content'
        );
    }

    public function createGrid($type, $code) {
        $grid = parent::createGrid($type, $code);
        $grid->addAction('content_update', array('code' => $this->getCode()), 'Edit', 'pencil');
        $queryBuilder = $this->environment->createQueryBuilder();
        $queryBuilder
            ->select('e')
            ->from($this->getParam('entity_class'), 'e');
        $grid->setQueryBuilder($queryBuilder);
        return $grid;
    }

    public function getCreateRoute() {
        return $this->environment->buildRoute('content_create', array('code' => $this->code));
    }

    public function getUpdateRoute($entity) {
        return $this->environment->buildRoute('content_update', array('code' => $this->code, 'id' => $entity->getId()));
    }

    public function validateParams(array $params) {
        if(!array_key_exists('entity_class', $params)) {
            throw new Exception(sprintf('The admin section %s must be configured with a "entity_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
        elseif(!class_exists($params['entity_class'])) {
            throw new Exception(sprintf('The admin section %s has an invalid "entity_class" parameter', $this->getCode()), Exception::SECTION_INVALID);
        }
    }
}