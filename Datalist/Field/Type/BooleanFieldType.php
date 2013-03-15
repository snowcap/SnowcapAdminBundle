<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

class BooleanFieldType extends AbstractFieldType
{

    public function __construct()
    {
        //trigger_error('The "boolean" field type is deprecated. Please use the "label" field type instead', E_USER_DEPRECATED);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'true_label' => null,
                'false_label' => null
            ))
            ->setAllowedTypes(array(
                'true_label' => array('null', 'string'),
                'false_label' => array('null', 'string'),
            ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $row, array $options)
    {
        parent::buildViewContext($viewContext, $field, $row, $options);

        $viewContext['true_label'] = $options['true_label'];
        $viewContext['false_label'] = $options['false_label'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'boolean';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'boolean';
    }
}