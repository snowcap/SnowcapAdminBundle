<?php

namespace Snowcap\AdminBundle\Datalist\Field;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

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
     * @param string $name
     * @param $value
     * @return mixed|void
     */
    public function setOption($name, $value)
    {
        $this->config->setOption($name, $value);
    }

    /**
     * @param mixed $row
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function getData($row)
    {
        $accessor = PropertyAccess::getPropertyAccessor();
        $propertyPath = $this->getPropertyPath();
        try {
            $value = $accessor->getValue($row, $propertyPath);
        } catch (NoSuchPropertyException $e) {
            if (is_object($row) && !$this->getDatalist()->hasOption('data_class')) {
                $message = sprintf('Missing "data_class" option');
            } else {
                $message = sprintf('unknown property "%s"', $propertyPath);
            }
            throw new \UnexpectedValueException($message);
        } catch (UnexpectedTypeException $e) {
            $value = null;
        }

        if(null === $value && $this->hasOption('default')) {
            $value = $this->getOption('default');
        }

        return $value;
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

    /**
     * @return string
     *
     * TODO: check if not better handled through options
     */
    private function getPropertyPath()
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