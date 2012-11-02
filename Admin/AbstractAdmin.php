<?php

namespace Snowcap\AdminBundle\Admin;

abstract class AbstractAdmin implements AdminInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
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
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name)
    {
        if(!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option with name "%s" does not exist', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }
}