<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression;

class EntityFilterType extends AbstractFilterType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array('query_builder' => null))
            ->setRequired(array('class'))
            ->setOptional(array('property', 'empty_value', 'group_by'));
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options)
    {
        $formOptions = array(
            'class' => $options['class'],
            'label' => $options['label'],
            'query_builder' => $options['query_builder'],
            'required' => false
        );
        if(isset($options['property'])) {
            $formOptions['property'] = $options['property'];
        }
        if (isset($options['empty_value'])) {
            $formOptions['empty_value'] = $options['empty_value'];
        }
        if (isset($options['group_by'])) {
            $formOptions['group_by'] = $options['group_by'];
        }

        $builder->add($filter->getName(), 'entity', $formOptions);
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
        return 'entity';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'entity';
    }
}