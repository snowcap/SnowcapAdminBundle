<?php

namespace Snowcap\AdminBundle\Datalist\Filter;

use Snowcap\AdminBundle\Datalist\DatalistInterface;

class DatalistFilter implements DatalistFilterInterface
{
    /**
     * @var DatalistFilterConfig
     */
    private $config;

    /**
     * @var
     */
    private $datalist;

    /**
     * @param DatalistFilterConfig $config
     */
    public function __construct(DatalistFilterConfig $config)
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
     * @return \Snowcap\AdminBundle\Datalist\Filter\Type\FilterTypeInterface
     */
    public function getType()
    {
        return $this->config->getType();
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     */
    public function setDatalist(DatalistInterface $datalist)
    {
        $this->datalist = $datalist;
    }

    /**
     * @return DatalistInterface
     */
    public function getDatalist()
    {
        return $this->datalist;
    }

    /**
     * @return string
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
}