<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'property_path' => null,
                'default' => null,
                'escape' => true,
            ))
            ->setOptional(array('callback', 'order'))
            ->setAllowedTypes(array('callback' => 'callable'));
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