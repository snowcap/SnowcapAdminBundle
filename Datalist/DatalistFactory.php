<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface;
use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;

class DatalistFactory
{
    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $fieldTypes = array();

    /**
     * @param string $type
     * @param string $view
     * @param array $options
     */
    public function create($type = 'datalist', array $options = array())
    {
        return $this->createBuilder($type, $options)->getDatalist();
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $view
     * @param array $options
     * @return Datalist
     */
    public function createNamed($name, $type = 'datalist', array $options = array())
    {
        return $this->createNamedBuilder($name, $type, $options)->getDatalist();
    }

    /**
     * @param mixed $type
     * @param string $view
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
     * @param string $view
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
        $type->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);

        // Build datalist
        $builder = new DatalistBuilder($name, $type, $resolvedOptions, $this);
        $type->buildDatalist($builder, $options);

        return $builder;
    }

    /**
     * @return DatalistTypeInterface
     */
    private function getType($alias)
    {
        if (!array_key_exists($alias, $this->types)) {
            throw new \InvalidArgumentException(sprintf('Unkown type "%s"', $alias));
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
     * @return FieldTypeInterface
     */
    public function getFieldType($alias)
    {
        if (!array_key_exists($alias, $this->fieldTypes)) {
            throw new \InvalidArgumentException(sprintf('Unkown type "%s"', $alias));
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
}