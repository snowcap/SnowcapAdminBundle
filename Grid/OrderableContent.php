<?php
namespace Snowcap\AdminBundle\Grid;

use Doctrine\ORM\QueryBuilder,
    Snowcap\AdminBundle\Exception;

class OrderableContent extends Content {
    public function getType() {
        return 'orderablecontent';
    }

    public function generateEntityKey($entity)
    {
        $parts = explode('\\', get_class($entity));
        $last = array_pop($parts);
        return strtolower($last);
    }

    public function getOrderForm() {
        $dataRows = array();
        foreach($this->getData() as $entity) {
            // create, if needed, a "class" key in dataRows
            $classOffset = $this->generateEntityKey($entity);
            if(!array_key_exists($classOffset, $dataRows)) {
                $dataRows[$classOffset] = array();
            }
            $dataRows[$classOffset][(string) $entity->getId()] = array(
                $this->getOption('order_field') => call_user_func(array($entity, 'get' . ucfirst($this->getOption('order_field'))))
            );
        }
        $builder = $this->formFactory->createBuilder('masscontent', $dataRows);
        return $builder->getForm();
    }

    public function getDragLink($entity) {
        return 'masscontent_' . $this->generateEntityKey($entity) . '_' . $entity->getId() . '_' . $this->getOption('order_field');
    }

    public function getOrderFormView() {
        return $this->getOrderForm()->createView();
    }

    public function getOrderFormAction() {
        return $this->router->generate('content_mass_update', array('code' => $this->code));
    }

    public function processQueryBuilder() {
        if(!$this->getOption('order_field')) {
            throw new Exception(sprintf('The "%s" grid needs a "order_field" parameter', $this->getName()), Exception::GRID_INVALID);
        }
        $this->queryBuilder->orderBy('e.' . $this->getOption('order_field'), 'ASC');
    }
}