<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDatasource implements DatasourceInterface
{
    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $limitPerPage;

    /**
     * @var int
     */
    protected $rangeLimit;

    /**
     * @var string
     */
    protected $searchQuery;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->processOptions($options);
    }

    /**
     * @param int $limitPerPage
     * @param int $limitRange
     *
     * @return AbstractDatasource
     */
    public function paginate($limitPerPage, $rangeLimit)
    {
        $this->limitPerPage = $limitPerPage;
        $this->rangeLimit = $rangeLimit;

        return $this;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param string $query
     */
    public function setSearchQuery($query)
    {
        $this->searchQuery = $query;
    }

    /**
     * @param array $options
     */
    protected function processOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setOptional(array('search', 'search_mode'))
            ->setAllowedTypes(array(
                    'search' => array('string', 'array')
                ));

        $this->options = $resolver->resolve($options);
    }
}