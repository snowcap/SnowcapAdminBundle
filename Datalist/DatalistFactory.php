<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\Action\Type\ActionTypeInterface;
use Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface;
use Snowcap\AdminBundle\Datalist\Filter\Type\FilterTypeInterface;
use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DatalistFactory
 * @package Snowcap\AdminBundle\Datalist
 */
class DatalistFactory
{
    /**
     * @var array
     */
    private $types = array();

    /**
     * @var array
     */
    private $fieldTypes = array();

    /**
     * @var array
     */
    private $filterTypes = array();

    /**
     * @var array
     */
    private $actionTypes = array();

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $type
     * @param array $options
     * @return DatalistInterface
     */
    public function create($type = 'datalist', array $options = array())
    {
        return $this->createBuilder($type, $options)->getDatalist();
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @return Datalist
     */
    public function createNamed($name, $type = 'datalist', array $options = array())
    {
        return $this->createNamedBuilder($name, $type, $options)->getDatalist();
    }

    /**
     * @param mixed $type
     * @param array $options
     * @return DatalistBuilder
     */
    public function createBuilder($type = 'datalist', array $options = array())
    {
        $name = $type instanceof DatalistTypeInterface
            ? $type->getName()
            : $type;

        return $this->createNamedBuilder($name, $type, $options);
    }

    /**
     * @param $name
     * @param mixed $type
     * @param array $options
     * @return DatalistBuilder
     * @throws \InvalidArgumentException
     */
    public function createNamedBuilder($name, $type = 'datalist', array $options = array())
    {
        // Determine datalist type
        if (is_string($type)) {
            $type = $this->getType($type);
        } elseif (!$type instanceof DatalistTypeInterface) {
            throw new \InvalidArgumentException(sprintf(
                'The type must be a string or an instance of DatalistTypeInterface'
            ));
        }

        // Handle datalist options
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);

        // Build datalist
        $builder = new DatalistBuilder($name, $type, $resolvedOptions, $this, $this->formFactory);
        $type->buildDatalist($builder, $resolvedOptions);

        return $builder;
    }

    /**
     * @return DatalistTypeInterface
     */
    public function getType($alias)
    {
        if (!array_key_exists($alias, $this->types)) {
            throw new \InvalidArgumentException(sprintf('Unknown type "%s"', $alias));
        }

        return $this->types[$alias];
    }

    /**
     * @param string $alias
     * @param Type\DatalistTypeInterface $type
     */
    public function registerType($alias, DatalistTypeInterface $type)
    {
        $this->types[$alias] = $type;
    }

    /**
     * @param $alias
     * @return FieldTypeInterface
     * @throws \InvalidArgumentException
     */
    public function getFieldType($alias)
    {
        if (!array_key_exists($alias, $this->fieldTypes)) {
            throw new \InvalidArgumentException(sprintf('Unknown field type "%s"', $alias));
        }

        return $this->fieldTypes[$alias];
    }

    /**
     * @param string $alias
     * @param \Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface $fieldType
     */
    public function registerFieldType($alias, FieldTypeInterface $fieldType)
    {
        $this->fieldTypes[$alias] = $fieldType;
    }

    /**
     * @param string $alias
     * @return FilterTypeInterface
     * @throws \InvalidArgumentException
     */
    public function getFilterType($alias)
    {
        if (!array_key_exists($alias, $this->filterTypes)) {
            throw new \InvalidArgumentException(sprintf('Unknown filter type "%s"', $alias));
        }

        return $this->filterTypes[$alias];
    }

    /**
     * @param string $alias
     * @param Filter\Type\FilterTypeInterface $filterType
     */
    public function registerFilterType($alias, FilterTypeInterface $filterType)
    {
        $this->filterTypes[$alias] = $filterType;
    }

    /**
     * @param string $alias
     * @return FilterTypeInterface
     * @throws \InvalidArgumentException
     */
    public function getActionType($alias)
    {
        if (!array_key_exists($alias, $this->actionTypes)) {
            throw new \InvalidArgumentException(sprintf('Unknown action type "%s"', $alias));
        }

        return $this->actionTypes[$alias];
    }

    /**
     * @param string $alias
     * @param Action\Type\ActionTypeInterface $actionType
     */
    public function registerActionType($alias, ActionTypeInterface $actionType)
    {
        $this->actionTypes[$alias] = $actionType;
    }
}