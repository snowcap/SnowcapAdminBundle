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
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $row, array $options)
    {
        parent::buildViewContext($viewContext, $field, $row, $options);

        $mappings = $options['mappings'];
        if(!array_key_exists($viewContext['value'], $mappings)) {
            throw new \UnexpectedValueException(sprintf('No mapping for value %s', $viewContext['value']));
        }

        $mapping = $mappings[$viewContext['value']];
        if(!is_array($mapping)) {
            throw new \Exception('mappings for the label field type must be specified as an associative array');
        }

        $viewContext['attr'] = isset($mapping['attr']) ? $mapping['attr'] : array();
        $viewContext['value'] = $mapping['label'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'label';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'label';
    }
}