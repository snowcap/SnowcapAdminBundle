<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

class LabelFieldType extends AbstractFieldType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setRequired(array('mappings'))
            ->setAllowedTypes(array(
                'mappings' => 'array'
            ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $value, array $options)
    {
        parent::buildViewContext($viewContext, $field, $value, $options);

        $mappings = $options['mappings'];
        if(!array_key_exists($value, $mappings)) {
            throw new \UnexpectedValueException(sprintf('No mapping for value %s', $value));
        }

        $viewContext['class'] = $value;
        $viewContext['value'] = $mappings[$value];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'label';
    }
}