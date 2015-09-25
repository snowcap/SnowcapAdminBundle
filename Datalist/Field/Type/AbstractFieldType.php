<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractFieldType
 * @package Snowcap\AdminBundle\Datalist\Field\Type
 */
abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'property_path' => null,
                'default' => null,
                'escape' => true,
                'sortable' => false,
                'sort_property_path' => null
            ))
            ->setDefined(array('callback', 'order'))
            ->setAllowedTypes('callback', 'callable');
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $row
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $row, array $options)
    {
        if(isset($options['callback'])) {
            $viewContext['value'] = call_user_func($options['callback'], $row);
        }
        else {
            $viewContext['value'] = $field->getData($row);
        }

        $viewContext['field'] = $field;
        $viewContext['options'] = $options;
        $viewContext['translation_domain'] = $field->getDatalist()->getOption('translation_domain');
    }
}