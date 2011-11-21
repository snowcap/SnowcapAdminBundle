<?php
namespace Snowcap\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder,
Symfony\Component\Form\AbstractType,
Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Form\ContentType,
Snowcap\AdminBundle\Grid\Content as ContentGrid;

/**
 * Content admin class
 *
 * Instances of this class are used as configuration for specific models
 */
abstract class Content extends Base
{

    public function getContentType() {
        $type = new ContentType();
        $this->configureContentType($type);
        return $type;
    }

    abstract public function configureContentType($type);

    public function createContentGrid($type, $options = array()) {
        $grid = $this->createGrid($type, $options);
        $queryBuilder = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from($this->getParam('entity_class'), 'e');
        $grid->setQueryBuilder($queryBuilder);
        $grid->setOption('section', $this->getParam('section'));
        $grid->addAction('content_update', array('section' => $this->getParam('section')), 'update', 'pencil');
        $grid->addAction('content_delete', array('section' => $this->getParam('section')), 'delete', 'trash');
        return $grid;
    }

    abstract function getContentGrid();

    public function getContentForm($data = null){
        $type = $this->getContentType();
        return $this->createForm($type, $data);
    }

    abstract public function getContentTitle($content);
}