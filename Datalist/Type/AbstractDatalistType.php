<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\DatalistInterface;

abstract class AbstractDatalistType implements DatalistTypeInterface {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'layout' => 'grid',
            'limit_per_page' => null,
            'range_limit' => 10,
            'searchable' => false,
            'search_placeholder' => 'datalist.search.placeholder',
            'search_submit' => 'datalist.search.submit',
            'filter_submit' => 'datalist.filter.submit',
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     */
    public function buildDatalist(DatalistBuilder $builder, array $options)
    {

    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistInterface $datalist, array $options)
    {
        $viewContext['datalist'] = $datalist;
        $viewContext['options'] = $options;
    }
}