<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression;

class ChoiceFilterType extends AbstractFilterType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('choices'));
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options)
    {
        $builder->add($filter->getName(), 'choice', array(
            'choices' => $options['choices'],
            'label' => $options['label']
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder $builder
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param mixed $value
     * @param array $options
     */
    public function buildExpression(DatalistFilterExpressionBuilder $builder, DatalistFilterInterface $filter, $value, array $options)
    {
        $builder->add(new ComparisonExpression($filter, ComparisonExpression::OPERATOR_EQ, $value));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'choice';
    }
}