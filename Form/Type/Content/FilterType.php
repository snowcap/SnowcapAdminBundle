<?php

namespace Snowcap\AdminBundle\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Form\DataTransformer\ContentFilterTransformer;

class FilterType extends AbstractType {
    const OPERATOR_EQUAL = '=';

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_content_filter';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', $options['type'], $options['options']);
        $transformer = new ContentFilterTransformer($options['field'], $options['operator']);
        $builder->addModelTransformer($transformer, true);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array('type', 'field'))
            ->setAllowedTypes(array(
                'type' => 'string',
                'field' => 'string'
            ))
            ->setDefaults(array(
                'csrf_protection' => false,
                'operator' => self::OPERATOR_EQUAL,
                'options' => array()
            ));
    }
}