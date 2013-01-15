<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Snowcap\AdminBundle\Datalist\TypeInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Datasource\DatasourceInterface;

interface FilterTypeInterface extends TypeInterface
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options);
}