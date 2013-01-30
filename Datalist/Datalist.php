<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression;

class Datalist implements DatalistInterface, \Countable
{
    /**
     * @var DatalistConfig
     */
    private $config;

    /**
     * @var DatasourceInterface
     */
    private $datasource;

    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var DatalistFilterInterface
     */
    private $searchFilter;

    /**
     * @var array
     */
    private $actions = array();

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var string
     */
    private $searchQuery;

    /**
     * @var array
     */
    private $filterData = array();

    /**
     * @var Form
     */
    private $searchForm;

    /**
     * @var Form
     */
    private $filterForm;

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @param string $code
     * @param array $options
     */
    public function __construct(DatalistConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return Type\DatalistTypeInterface
     */
    public function getType()
    {
        return $this->config->getType();
    }

    /**
     * @param DatalistFieldInterface $field
     * @return Datalist
     */
    public function addField(DatalistFieldInterface $field)
    {
        $this->fields[] = $field;

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
     * @param Filter\DatalistFilterInterface $filter
     * @return DatalistInterface
     */
    public function addFilter(DatalistFilterInterface $filter)
    {
        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param Filter\DatalistFilterInterface $filter
     */
    public function setSearchFilter(DatalistFilterInterface $filter)
    {
        $this->searchFilter = $filter;
    }

    /**
     * @return Filter\DatalistFilterInterface
     */
    public function getSearchFilter()
    {
        return $this->searchFilter;
    }

    /**
     * @param Action\DatalistActionInterface $action
     * @return DatalistInterface
     */
    public function addAction(DatalistActionInterface $action)
    {
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param DatasourceInterface $datasource
     */
    public function setDatasource($datasource)
    {
        $this->datasource = $datasource;

        return $this;
    }

    /**
     * @return DatasourceInterface
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * @return \Snowcap\CoreBundle\Paginator\PaginatorInterface
     */
    public function getPaginator()
    {
        return $this->datasource->getPaginator();
    }

    /**
     * @param int $page
     *
     * @return DatalistInterface
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
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
     * @return bool
     */
    public function isFilterable()
    {
        return count($this->filters) > 0;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return DatalistInterface
     */
    public function setSearchForm(FormInterface $form)
    {
        $this->searchForm = $form;

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return DatalistInterface
     */
    public function setFilterForm(FormInterface $form)
    {
        $this->filterForm = $form;

        return $this;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getSearchForm()
    {
        return $this->searchForm;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getFilterForm()
    {
        return $this->filterForm;
    }

    /**
     * Bind search / filter data to the datalist
     *
     * @param mixed $data a data array, a Request instance or an arbitrary object
     * @return DatalistInterface
     */
    public function bind($data)
    {
        if($data instanceof Request) {
            $data = $data->query->all();
        }

        // Handle pagination
        if(isset($data['page'])) {
            $this->setPage($data['page']);
        }

        // Handle search
        if(isset($data['search'])) {
            $this->searchQuery = $data['search'];
            $this->searchForm->bind(array('search' => $data['search']));
        }

        // Handle filters
        foreach($this->filters as $filter) {
            if(isset($data[$filter->getName()]) && !empty($data[$filter->getName()])) {
                $this->filterData[$filter->getName()] = $data[$filter->getName()];
            }
        }
        $this->filterForm->bind($this->filterData);
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        $this->initialize();

        return $this->iterator;
    }

    /**
     * @return int
     */
    public function count()
    {
        $this->initialize();

        return count($this->datasource);
    }

    /**
     * This method populates the iterator property
     *
     */
    private function initialize()
    {
        if($this->initialized) {
            return;
        }

        if (!isset($this->datasource)) {
            $this->iterator = new \EmptyIterator();
        }

        // Handle pagination
        if ($this->hasOption('limit_per_page')) {
            $this->datasource
                ->paginate($this->getOption('limit_per_page'), $this->getOption('range_limit'))
                ->setPage($this->page);
        }

        // Handle search
        if (null !== $this->getOption('search') && !empty($this->searchQuery)) {
            $expressionBuilder = new DatalistFilterExpressionBuilder();
            $this->searchFilter->getType()->buildExpression($expressionBuilder, $this->searchFilter, $this->searchQuery, $this->searchFilter->getOptions());
            $this->datasource->setSearchExpression($expressionBuilder->getExpression());
        }

        // Handle filters
        $expressionBuilder = new DatalistFilterExpressionBuilder();
        if(!empty($this->filterData)) {
            foreach($this->filterData as $filterName => $filterValue) {
                $filter = $this->filters[$filterName];
                $filter->getType()->buildExpression($expressionBuilder, $filter, $filterValue, $filter->getOptions());
            }
            $this->datasource->setFilterExpression($expressionBuilder->getExpression());
        }

        $this->iterator = $this->datasource->getIterator();
    }
}