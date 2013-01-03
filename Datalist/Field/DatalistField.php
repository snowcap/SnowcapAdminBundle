<?php

namespace Snowcap\AdminBundle\Datalist\Field;

use Snowcap\AdminBundle\Datalist\DatalistInterface;
use Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface;

class DatalistField implements DatalistFieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var FieldTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var DatalistInterface
     */
    private $datalist;

    /**
     * @param string $name
     * @param FieldTypeInterface $type
     * @param array $options
     */
    public function __construct($name, FieldTypeInterface $type, array $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist', $name));
        }

        return $this->options[$name];
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        $propertyPath = $this->getOption('property_path');
        if (null === $propertyPath) {
            $propertyPath = $this->name;
            if (null === $this->datalist->getOption('data_class')) {
                $propertyPath = '[' . $propertyPath . ']';
            }
        }

        return $propertyPath;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return mixed
     */
    public function setDatalist(DatalistInterface $datalist)
    {
        $this->datalist = $datalist;
    }

    /**
     * @return \Snowcap\AdminBundle\Datalist\DatalistInterface
     */
    public function getDatalist()
    {
        return $this->datalist;
    }

    /**
     * @return \Snowcap\AdminBundle\Datalist\Field\Type\FieldTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }
}