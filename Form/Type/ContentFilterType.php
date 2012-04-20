<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Exception\MissingOptionsException;

use Snowcap\AdminBundle\Form\DataTransformer\ContentFilterTransformer;

class ContentFilterType extends AbstractType {

    const OPERATOR_EQUAL = '=';

    /**
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     * @throws \Symfony\Component\Form\Exception\MissingOptionsException
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if(!isset($options['type'])) {
            throw new MissingOptionsException('the "type" option is mandatory', array('type'));
        }
        if(!isset($options['field'])) {
            throw new MissingOptionsException('the "field" option is mandatory', array('type'));
        }
        $builder->add('value', $options['type'], $options['options']);
        $builder->appendNormTransformer(new ContentFilterTransformer($options['field'], $options['operator']));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'snowcap_admin_content_filter';
    }

    /**
     * @param array $options
     * @return string
     */
    public function getParent(array $options)
    {
        return 'form';
    }

    /**
     * @param array $options
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'field' => null,
            'operator' => self::OPERATOR_EQUAL,
            'type' => null,
            'options' => array(),
        );
    }


}