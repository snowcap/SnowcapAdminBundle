<?php

namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;

abstract class Config {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param $name
     * @param Type\DatalistTypeInterface $type
     * @param array $options
     */
    public function __construct($name, TypeInterface $type, array $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return bool
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * @param string $name
     * @param mixed $default
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @return \Snowcap\AdminBundle\Datalist\TypeInterface
     */
    public function getType()
    {
        return $this->type;
    }
}