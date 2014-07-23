<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Datalist\Type\DatalistTypeInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistField;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldConfig;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilter;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterConfig;
use Snowcap\AdminBundle\Datalist\Action\DatalistAction;
use Snowcap\AdminBundle\Datalist\Action\DatalistActionConfig;

class DatalistBuilder extends DatalistConfig
{
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var array
     */
    private $actions = array();

    /**
     * @var DatalistFactory
     */
    private $factory;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @param string $name
     * @param Type\DatalistTypeInterface $type
     * @param array $options
     * @param DatalistFactory $factory
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function __construct($name, DatalistTypeInterface $type, array $options, DatalistFactory $factory, FormFactory $formFactory)
    {
        parent::__construct($name, $type, $options);

        $this->factory = $factory;
        $this->formFactory = $formFactory;
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
     * @param $field
     * @return $this
     */
    public function removeField($field)
    {
        if (array_key_exists($field, $this->fields)) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $filter
     * @param string $type
     * @param array $options
     * @return DatalistBuilder
     */
    public function addFilter($filter, $type = null, array $options = array())
    {
        $this->filters[$filter] = array(
            'type' => $type,
            'options' => $options
        );

        return $this;
    }

    /**
     * @param $filter
     * @return $this
     */
    public function removeFilter($filter)
    {
        if (array_key_exists($filter, $this->filters)) {
            unset($this->filters[$filter]);
        }

        return $this;
    }

    /**
     * @param $action
     * @param string $type
     * @param array $options
     */
    public function addAction($action, $type = null, array $options = array())
    {
        $this->actions[$action] = array(
            'type' => $type,
            'options' => $options
        );

        return $this;
    }

    /**
     * @param $action
     * @return $this
     */
    public function removeAction($action)
    {
        if (array_key_exists($action, $this->actions)) {
            unset($this->actions[$action]);
        }

        return $this;
    }

    /**
     * @return DatalistInterface
     */
    public function getDatalist()
    {
        $datalist = new Datalist($this->getDatalistConfig());

        // Add fields
        foreach ($this->fields as $fieldName => $fieldConfig) {
            $field = $this->createField($fieldName, $fieldConfig);
            $field->setDatalist($datalist);
            $datalist->addField($field);
        }

        // Add search form
        if(null !== $this->getOption('search')) {
            $searchFormBuilder = $this->formFactory->createNamedBuilder('');
            $searchFilter = $this->createFilter('search', array(
                'type' => 'search',
                'options' => array(
                    'search_fields' => $datalist->getOption('search')
                )
            ));
            $searchFilter->getType()->buildForm($searchFormBuilder, $searchFilter, $searchFilter->getOptions());

            $searchFilter->setDatalist($datalist);
            $datalist->setSearchFilter($searchFilter);
            $datalist->setSearchForm($searchFormBuilder->getForm());
        }

        // Add filters and filter form
        $filterFormBuilder = $this->formFactory->createNamedBuilder('', 'form', null, array(
            'translation_domain' => $datalist->getOption('translation_domain')
        ));
        foreach ($this->filters as $filterName => $filterConfig) {
            $filter = $this->createFilter($filterName, $filterConfig);
            $filter->setDatalist($datalist);
            $filter->getType()->buildForm($filterFormBuilder, $filter, $filter->getOptions());
            $datalist->addFilter($filter);
        }
        $datalist->setFilterForm($filterFormBuilder->getForm());

        // Add actions
        foreach($this->actions as $actionName => $actionConfig) {
            $action = $this->createAction($actionName, $actionConfig);
            $action->setDatalist($datalist);
            $datalist->addAction($action);
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
        $type = $this->factory->getFieldType($fieldConfig['type'] ? : 'text');

        // Handle field options
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('label' => ucfirst($fieldName)));
        $type->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve($fieldConfig['options']);

        $config = new DatalistFieldConfig($fieldName, $type, $resolvedOptions);

        return new DatalistField($config);
    }

    /**
     * @param string $filterName
     * @param array $filterConfig
     * @return Filter\DatalistFilter
     */
    private function createFilter($filterName, array $filterConfig)
    {
        $type = $this->factory->getFilterType($filterConfig['type']);

        // Handle filter options
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('label' => ucfirst($filterName)));
        $type->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve($filterConfig['options']);

        $config = new DatalistFilterConfig($filterName, $type, $resolvedOptions);

        return new DatalistFilter($config);
    }

    private function createAction($actionName, array $actionConfig)
    {
        $type = $this->factory->getActionType($actionConfig['type'] ?: 'simple');

        // Handle action options
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('label' => ucfirst($actionName)));
        $type->setDefaultOptions($resolver);
        $resolvedOptions = $resolver->resolve($actionConfig['options']);

        $config = new DatalistActionConfig($actionName, $type, $resolvedOptions);

        return new DatalistAction($config);
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