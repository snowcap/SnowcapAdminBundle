<?php

namespace Snowcap\AdminBundle\Datalist\Field;

use Snowcap\AdminBundle\Datalist\DatalistInterface;

class DatalistField implements DatalistFieldInterface
{
    /**
     * @var DatalistFieldConfig
     */
    private $config;

    /**
     * @var DatalistInterface
     */
    private $datalist;

    /**
     * @param DatalistFieldConfig $config
     */
    public function __construct(DatalistFieldConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->config->getOptions();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->config->hasOption($name);
    }

    /**
     * @param string $name
     * @param mixed $default
     */
    public function getOption($name, $default = null)
    {
        return $this->config->getOption($name, $default);
    }

    /**
     * @return string
     *
     * TODO: check if not better handled through options
     */
    public function getPropertyPath()
    {
        $propertyPath = $this->getOption('property_path');
        if (null === $propertyPath) {
            $propertyPath = $this->config->getName();
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
        return $this->config->getType();
    }
}