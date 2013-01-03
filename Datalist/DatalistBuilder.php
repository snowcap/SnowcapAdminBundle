<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;
use Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistField;

class DatalistBuilder {
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var string
     */
    private $name;

    /**
     * @var Type\DatalistTypeInterface
     */
    private $type;

    /**
     * @var DatalistFactory
     */
    private $factory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $name
     * @param DatalistFactory $factory
     * @param array $options
     */
    public function __construct($name, DatalistTypeInterface $type, DatalistFactory $factory, array $options){
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
        $this->options = $options;
    }

    /**
     * @param $field
     * @param string $type
     * @param array $options
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
        $datalist = new Datalist($this->name, $this->type, $this->options);

        foreach($this->fields as $fieldName => $fieldConfig){
            $field = $this->createField($fieldName, $fieldConfig);
            $field->setDatalist($datalist);
            $datalist->addField($field);
        }

        return $datalist;
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
        $resolver = new OptionsResolver(); //TODO: should be done in FieldType base class, with form-like inheritance ?
        $resolver->setDefaults(array(
            'label' => ucfirst($fieldName)
        ));
        $this->resolveDatalistFieldTypeOptions($type, $resolver);
        $resolvedOptions = $resolver->resolve($fieldConfig['options']);

        return new DatalistField($fieldName, $type, $resolvedOptions);
    }

    /**
     * @param Field\Type\FieldTypeInterface $type
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    private function resolveDatalistFieldTypeOptions(FieldTypeInterface $type, OptionsResolver $optionsResolver)
    {
        if (null !== $type->getParent()) {
            $this->resolveDatalistFieldTypeOptions($type->getParent(), $optionsResolver);
        }

        $type->setDefaultOptions($optionsResolver);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}