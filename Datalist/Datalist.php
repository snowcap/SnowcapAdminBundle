<?php

namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;

class Datalist implements DatalistInterface
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
     * @return \Traversable
     */
    public function getIterator()
    {
        if (!isset($this->datasource)) {
            return new \EmptyIterator();
        }

        $datasource = $this->getDatasource();

        // Handle pagination
        if ($this->hasOption('limit_per_page')) {
            $datasource
                ->paginate($this->getOption('limit_per_page'), $this->getOption('range_limit'))
                ->setPage($this->page);
        }

        // Handle search
        if (true === $this->getOption('searchable') && !empty($this->searchQuery)) {
            $datasource->setSearchQuery($this->searchQuery);
        }

        // Handle filters
        if(!empty($this->filterData)) {
            foreach($this->filterData as $filterName => $filterValue) {
                $filter = $this->filters[$filterName];
                //TODO: do something with this
            }
        }

        return $datasource->getIterator();
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
    public function isSearchable()
    {
        return true === $this->getOption('searchable');
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return count($this->filters) > 0;
    }

    public function setSearchForm(Form $form)
    {
        $this->searchForm = $form;
    }

    public function setFilterForm(Form $form)
    {
        $this->filterForm = $form;
    }

    public function getSearchForm()
    {
        return $this->searchForm;
    }

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

        // Handle search
        if(isset($data['search'])) {
            $this->searchQuery = $data['search'];
            $this->searchForm->bind(array('search' => $data['search']));
        }

        // Handle filters
        foreach($this->filters as $filter) {
            $filterPropertyPath = $filter->getPropertyPath();
            $propertyPath = new PropertyPath($filterPropertyPath);
            $filterValue = $propertyPath->getValue($data);
            if(null !== $filterValue) {
                $this->filterData[$filter->getName()] = $data[$filter->getName()];
            }
            $this->filterForm->bind($this->filterData);
        }
    }


    public function addAction($routeName, array $parameters = array(), array $options = array())
    {
        $options = array_merge(
            array(
                'confirm' => false,
                'confirm_title' => 'content.actions.confirm.title',
                'confirm_body' => 'content.actions.confirm.body',
                'confirm_confirm' => 'content.actions.confirm.confirm',
                'confirm_cancel' => 'content.actions.confirm.cancel',
            ),
            $options
        );
        if (!array_key_exists('label', $options)) {
            $options['label'] = ucfirst($routeName);
        }
        $this->actions[$routeName] = array('parameters' => $parameters, 'options' => $options);

        return $this;
    }

    public function removeAction($routeName)
    {
        if (array_key_exists($routeName, $this->actions)) {
            unset($this->actions[$routeName]);
        }
    }

    public function getActions()
    {
        return $this->actions;
    }
}