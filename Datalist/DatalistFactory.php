<?php
namespace Snowcap\AdminBundle\Datalist;

use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;
use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;

class DatalistFactory {
    /**
     * @var array
     */
    protected $views = array();

    /**
     * @var array
     */
    protected $types = array();

    /**
     * @param string $type
     * @param string $view
     * @param array $options
     */
    public function create($type = 'datalist', $view = 'grid', array $options = array())
    {
        return $this->createBuilder($type, $view, $options)->getDatalist();
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $view
     * @param array $options
     * @return Datalist
     */
    public function createNamed($name, $type = 'datalist', $view = 'grid', array $options = array())
    {
        return $this->createNamedBuilder($name, $type, $view, $options)->getDatalist();
    }

    /**
     * @param mixed $type
     * @param string $view
     * @param array $options
     * @return DatalistBuilder
     */
    public function createBuilder($type = 'datalist', $view = 'grid', array $options = array())
    {
        $name = $type instanceof DatalistTypeInterface
            ? $type->getName()
            : $type;

        return $this->createNamedBuilder($name, $type, $view, $options);
    }

    /**
     * @param $name
     * @param mixed $type
     * @param string $view
     * @param array $options
     * @return DatalistBuilder
     * @throws \InvalidArgumentException
     */
    public function createNamedBuilder($name, $type = 'datalist', $view = 'grid', array $options = array())
    {
        if (is_string($type)) {
            $type = $this->getType($type);
        } elseif (!$type instanceof DatalistTypeInterface) {
            throw new \InvalidArgumentException(sprintf('The type must be a string or an instance of DatalistTypeInterface'));
        }

        if (is_string($view)) {
            $view = $this->getView($view);
        } elseif (!$view instanceof DatalistViewInterface) {
            throw new \InvalidArgumentException(sprintf('The view must be a string or an instance of DatalistViewInterface'));
        }

        $builder = new DatalistBuilder($name, $view, $options);
        $type->buildDatalist($builder, $options);

        return $builder;
    }

    /**
     * @return DatalistTypeInterface
     */
    private function getType($type)
    {
        if(!array_key_exists($type, $this->types)){
            throw new \InvalidArgumentException(sprintf('Unkown type "%s"', $type));
        }

        return $this->types[$type];
    }

    /**
     * @return DatalistViewInterface
     */
    private function getView($view)
    {
        if(!array_key_exists($view, $this->views)){
            throw new \InvalidArgumentException(sprintf('Unkown view "%s"', $view));
        }

        return $this->views[$view];
    }

    /**
     * @param string $alias
     * @param View\DatalistViewInterface $view
     */
    public function addView($alias, DatalistViewInterface $view) {
        $this->views[$alias] = $view;
    }

    /**
     * @param string $alias
     * @param Type\DatalistTypeInterface $type
     */
    public function addType($alias, DatalistTypeInterface $type) {
        $this->types[$alias] = $type;
    }
}