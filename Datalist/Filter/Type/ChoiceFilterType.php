<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Type;

use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChoiceFilterType
 * @package Snowcap\AdminBundle\Datalist\Filter\Type
 */
class ChoiceFilterType extends AbstractFilterType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(array('choices'))
            ->setDefined(array('empty_value', 'preferred_choices'));
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options)
    {
        $formOptions = array(
            'choices' => $options['choices'],
            'label' => $options['label'],
            'required' => false
        );
        if(isset($options['empty_value'])) {
            $formOptions['empty_value'] = $options['empty_value'];
        }
        if(isset($options['preferred_choices'])) {
            $formOptions['preferred_choices'] = $options['preferred_choices'];
        }

        $builder->add($filter->getName(), 'choice', $formOptions);
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder $builder
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param mixed $value
     * @param array $options
     */
    public function buildExpression(DatalistFilterExpressionBuilder $builder, DatalistFilterInterface $filter, $value, array $options)
    {
        $builder->add(new ComparisonExpression($filter->getPropertyPath(), ComparisonExpression::OPERATOR_EQ, $value));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'choice';
    }
}