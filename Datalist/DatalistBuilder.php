<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistField;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldConfig;

class DatalistBuilder extends DatalistConfig {
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var DatalistFactory
     */
    private $factory;

    /**
     * @param string $name
     * @param DatalistFactory $factory
     * @param array $options
     */
    public function __construct($name, DatalistTypeInterface $type, array $options, DatalistFactory $factory){
        parent::__construct($name, $type, $options);
        $this->factory = $factory;
    }

    /**
     * @param string $field
     * @param string $type
     * @param array $options
     * @return DatalistBuilder
     */
    public function addField($field, $type = null, array $options = array())
    {
        $this->fields[$field] = array(
            'type' => $type,
            'options' => $options
        );

        return $this;
    }

    /**
     * @return DatalistInterface
     */
    public function getDatalist()
    {
        $datalist = new Datalist($this->getDatalistConfig());

        foreach($this->fields as $fieldName => $fieldConfig){
            $field = $this->createField($fieldName, $fieldConfig);
            $field->setDatalist($datalist);
            $datalist->addField($field);
        }

        return $datalist;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $fieldName
     * @param array $fieldConfig
     * @return \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface
     */
    private function createField($fieldName, array $fieldConfig)
    {
        $type = $this->factory->getFieldType($fieldConfig['type'] ?: 'text');

        // Handle field options
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'label' => ucfirst($fieldName)
        ));
        $type->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve($fieldConfig['options']);

        $config = new DatalistFieldConfig($fieldName, $type, $resolvedOptions);

        return new DatalistField($config);
    }

    /**
     * @return DatalistBuilder
     */
    private function getDatalistConfig()
    {
        $config = clone $this;

        return $config;
    }
}